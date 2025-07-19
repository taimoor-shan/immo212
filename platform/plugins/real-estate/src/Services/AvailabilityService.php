<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\PropertyAvailability;
use Botble\RealEstate\Models\PropertyAvailabilityRule;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Models\PropertyCalendarEvent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;

class AvailabilityService
{
    /**
     * Check if a property is available for the given date range
     */
    public function checkAvailability(int $propertyId, Carbon $checkIn, Carbon $checkOut): bool
    {
        Log::info('Checking availability', [
            'property_id' => $propertyId,
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d')
        ]);

        // Check for existing bookings
        $hasBookingConflict = VacationRentalBooking::checkDateConflict($propertyId, $checkIn, $checkOut);
        if ($hasBookingConflict) {
            Log::info('Availability check failed: booking conflict');
            return false;
        }

        // Check availability records
        $hasAvailabilityRecords = PropertyAvailability::checkAvailability($propertyId, $checkIn, $checkOut);
        if (!$hasAvailabilityRecords) {
            Log::info('Availability check failed: availability records');
            return false;
        }

        // Check availability rules for blocked periods (use copy to avoid mutating original dates)
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
        foreach ($period as $date) {
            if (PropertyAvailabilityRule::isDateBlocked($propertyId, $date)) {
                Log::info('Availability check failed: blocked by rule', ['date' => $date->format('Y-m-d')]);
                return false;
            }
        }

        Log::info('Availability check passed');
        return true;
    }

    /**
     * Get detailed availability information for a date range
     */
    public function getAvailabilityDetails(int $propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $property = Property::findOrFail($propertyId);
        $availability = PropertyAvailability::getAvailabilityForDateRange($propertyId, $startDate, $endDate);
        
        // Apply availability rules to enhance the data
        foreach ($availability as $date => &$dayInfo) {
            $carbonDate = Carbon::parse($date);
            
            // Apply pricing rules
            $basePrice = $property->price ?? 0;
            $effectivePrice = PropertyAvailabilityRule::calculateEffectivePrice($propertyId, $carbonDate, $basePrice);
            
            // Apply minimum stay rules
            $defaultMinStay = $property->minimum_stay ?? 1;
            $effectiveMinStay = PropertyAvailabilityRule::getEffectiveMinimumStay($propertyId, $carbonDate, $defaultMinStay);
            
            // Check if date is blocked by rules (only if not already blocked in availability table)
            if ($dayInfo['status'] !== PropertyAvailability::STATUS_BLOCKED &&
                PropertyAvailabilityRule::isDateBlocked($propertyId, $carbonDate)) {
                $dayInfo['status'] = PropertyAvailability::STATUS_BLOCKED;
                $dayInfo['color'] = '#dc3545';
            }
            
            // Update with calculated values
            if (!$dayInfo['price']) {
                $dayInfo['price'] = $effectivePrice;
            }
            $dayInfo['minimum_stay'] = $effectiveMinStay;
            $dayInfo['base_price'] = $basePrice;
            $dayInfo['price_modifier'] = $basePrice > 0 ? ($effectivePrice / $basePrice) : 1;
        }
        
        return $availability;
    }

    /**
     * Get only non-available dates (exceptions) for performance optimization
     */
    public function getAvailabilityExceptions(int $propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $property = Property::findOrFail($propertyId);

        // Get only non-available dates from the database
        $exceptions = PropertyAvailability::forProperty($propertyId)
            ->inDateRange($startDate, $endDate)
            ->whereIn('status', [
                PropertyAvailability::STATUS_BOOKED,
                PropertyAvailability::STATUS_BLOCKED,
                PropertyAvailability::STATUS_MAINTENANCE
            ])
            ->orderBy('date')
            ->get();

        $result = [];

        foreach ($exceptions as $exception) {
            $carbonDate = Carbon::parse($exception->date);
            $dateKey = $carbonDate->format('Y-m-d');

            // Apply pricing rules
            $basePrice = $property->price ?? 0;
            $effectivePrice = PropertyAvailabilityRule::calculateEffectivePrice($propertyId, $carbonDate, $basePrice);

            // Apply minimum stay rules
            $defaultMinStay = $property->minimum_stay ?? 1;
            $effectiveMinStay = PropertyAvailabilityRule::getEffectiveMinimumStay($propertyId, $carbonDate, $defaultMinStay);

            $result[$dateKey] = [
                'date' => $dateKey,
                'status' => $exception->status,
                'price' => $exception->getEffectivePrice() ?: $effectivePrice,
                'minimum_stay' => $exception->getEffectiveMinimumStay() ?: $effectiveMinStay,
                'color' => $exception->getStatusColor(),
                'notes' => $exception->notes,
                'base_price' => $basePrice,
                'price_modifier' => $basePrice > 0 ? (($exception->getEffectivePrice() ?: $effectivePrice) / $basePrice) : 1,
            ];
        }

        // Also check for dates blocked by rules that don't have explicit availability records
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dateKey = $date->format('Y-m-d');

            // Skip if we already have an exception for this date
            if (isset($result[$dateKey])) {
                continue;
            }

            // Check if date is blocked by rules
            if (PropertyAvailabilityRule::isDateBlocked($propertyId, $date)) {
                $basePrice = $property->price ?? 0;
                $effectivePrice = PropertyAvailabilityRule::calculateEffectivePrice($propertyId, $date, $basePrice);
                $defaultMinStay = $property->minimum_stay ?? 1;
                $effectiveMinStay = PropertyAvailabilityRule::getEffectiveMinimumStay($propertyId, $date, $defaultMinStay);

                $result[$dateKey] = [
                    'date' => $dateKey,
                    'status' => PropertyAvailability::STATUS_BLOCKED,
                    'price' => $effectivePrice,
                    'minimum_stay' => $effectiveMinStay,
                    'color' => '#dc3545',
                    'notes' => 'Blocked by rule',
                    'base_price' => $basePrice,
                    'price_modifier' => $basePrice > 0 ? ($effectivePrice / $basePrice) : 1,
                ];
            }
        }

        return $result;
    }

    /**
     * Calculate pricing for a booking
     */
    public function calculateBookingPrice(int $propertyId, Carbon $checkIn, Carbon $checkOut, int $guests = 1): array
    {
        $property = Property::findOrFail($propertyId);

        $nights = $checkIn->diffInDays($checkOut);

        if ($nights <= 0) {
            throw new \InvalidArgumentException('Check-out date must be after check-in date');
        }

        // Validate guest count against property maximum
        if ($property->maximum_guests && $guests > $property->maximum_guests) {
            throw new \InvalidArgumentException("Number of guests ({$guests}) exceeds property maximum ({$property->maximum_guests})");
        }

        $totalNightsCost = 0;
        $priceBreakdown = [];
        
        // Calculate nightly rates (use copy to avoid mutating original dates)
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
        foreach ($period as $date) {
            $basePrice = $property->price ?? 0;
            $nightPrice = PropertyAvailabilityRule::calculateEffectivePrice($propertyId, $date, $basePrice);
            
            // Check for specific date overrides
            $availability = PropertyAvailability::forProperty($propertyId)
                ->where('date', $date->format('Y-m-d'))
                ->first();
                
            if ($availability && $availability->price_per_night) {
                $nightPrice = $availability->price_per_night;
            }
            
            $totalNightsCost += $nightPrice;
            $priceBreakdown[] = [
                'date' => $date->format('Y-m-d'),
                'price' => $nightPrice,
                'base_price' => $basePrice,
            ];
        }

        // Additional fees
        $cleaningFee = $property->cleaning_fee ?? 0;
        $securityDeposit = $property->security_deposit ?? 0;
        $serviceFee = $totalNightsCost * 0.03; // 3% service fee
        $taxes = ($totalNightsCost + $serviceFee) * 0.12; // 12% taxes

        $totalAmount = $totalNightsCost + $cleaningFee + $serviceFee + $taxes;

        return [
            'nights' => $nights,
            'guests' => $guests,
            'base_price_per_night' => $property->price ?? 0,
            'total_nights_cost' => $totalNightsCost,
            'cleaning_fee' => $cleaningFee,
            'security_deposit' => $securityDeposit,
            'service_fee' => $serviceFee,
            'taxes' => $taxes,
            'total_amount' => $totalAmount,
            'price_breakdown' => $priceBreakdown,
            'average_nightly_rate' => $nights > 0 ? $totalNightsCost / $nights : 0,
        ];
    }

    /**
     * Block dates for a property
     */
    public function blockDates(int $propertyId, Carbon $startDate, Carbon $endDate, string $reason = 'Blocked by owner'): void
    {
        // Create calendar event
        PropertyCalendarEvent::createBlockedPeriod($propertyId, $startDate, $endDate, 'Blocked', $reason);

        // Update availability records
        $dates = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        PropertyAvailability::bulkUpdateAvailability($propertyId, $dates, [
            'status' => PropertyAvailability::STATUS_BLOCKED,
            'notes' => $reason,
        ]);
    }

    /**
     * Unblock dates for a property
     */
    public function unblockDates(int $propertyId, Carbon $startDate, Carbon $endDate): void
    {
        // Remove calendar events
        PropertyCalendarEvent::forProperty($propertyId)
            ->ofType(PropertyCalendarEvent::TYPE_BLOCKED)
            ->inDateRange($startDate, $endDate)
            ->delete();

        // Update availability records
        $dates = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        PropertyAvailability::bulkUpdateAvailability($propertyId, $dates, [
            'status' => PropertyAvailability::STATUS_AVAILABLE,
            'notes' => null,
        ]);
    }

    /**
     * Set dates to maintenance for a property
     */
    public function maintenanceDates(int $propertyId, Carbon $startDate, Carbon $endDate, string $reason = 'Maintenance'): void
    {
        // Create calendar event
        PropertyCalendarEvent::createMaintenancePeriod($propertyId, $startDate, $endDate, 'Maintenance', $reason);

        // Update availability records
        $dates = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        PropertyAvailability::bulkUpdateAvailability($propertyId, $dates, [
            'status' => PropertyAvailability::STATUS_MAINTENANCE,
            'notes' => $reason,
        ]);
    }

    /**
     * Get calendar events for display
     */
    public function getCalendarEvents(int $propertyId, Carbon $startDate, Carbon $endDate): array
    {
        return PropertyCalendarEvent::getEventsForCalendar($propertyId, $startDate, $endDate);
    }

    /**
     * Get monthly availability summary
     */
    public function getMonthlyAvailabilitySummary(int $propertyId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $availability = $this->getAvailabilityDetails($propertyId, $startDate, $endDate);
        $events = PropertyCalendarEvent::getMonthlyEvents($propertyId, $year, $month);
        
        $summary = [
            'total_days' => count($availability),
            'available_days' => 0,
            'booked_days' => 0,
            'blocked_days' => 0,
            'maintenance_days' => 0,
            'total_revenue' => 0,
            'average_rate' => 0,
            'occupancy_rate' => 0,
        ];
        
        foreach ($availability as $dayInfo) {
            switch ($dayInfo['status']) {
                case PropertyAvailability::STATUS_AVAILABLE:
                    $summary['available_days']++;
                    break;
                case PropertyAvailability::STATUS_BOOKED:
                    $summary['booked_days']++;
                    $summary['total_revenue'] += $dayInfo['price'];
                    break;
                case PropertyAvailability::STATUS_BLOCKED:
                    $summary['blocked_days']++;
                    break;
                case PropertyAvailability::STATUS_MAINTENANCE:
                    $summary['maintenance_days']++;
                    break;
            }
        }
        
        if ($summary['booked_days'] > 0) {
            $summary['average_rate'] = $summary['total_revenue'] / $summary['booked_days'];
        }
        
        $summary['occupancy_rate'] = $summary['total_days'] > 0 
            ? ($summary['booked_days'] / $summary['total_days']) * 100 
            : 0;
        
        return [
            'summary' => $summary,
            'availability' => $availability,
            'events' => $events,
        ];
    }

    /**
     * Validate minimum stay requirements
     */
    public function validateMinimumStay(int $propertyId, Carbon $checkIn, Carbon $checkOut): bool
    {
        $nights = $checkIn->diffInDays($checkOut);
        $property = Property::findOrFail($propertyId);

        $minimumStay = PropertyAvailabilityRule::getEffectiveMinimumStay(
            $propertyId,
            $checkIn,
            $property->minimum_stay ?? 1
        );

        return $nights >= $minimumStay;
    }

    /**
     * Get the effective minimum stay for a property on a specific date
     */
    public function getEffectiveMinimumStay(int $propertyId, Carbon $checkIn): int
    {
        $property = Property::findOrFail($propertyId);

        return PropertyAvailabilityRule::getEffectiveMinimumStay(
            $propertyId,
            $checkIn,
            $property->minimum_stay ?? 1
        );
    }
}

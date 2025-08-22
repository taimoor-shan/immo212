<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\VacationRental;
use Botble\RealEstate\Models\VacationRentalAvailability;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SaveVacationRentalAvailabilityService
{
    public function execute(VacationRental $vacationRental, array $availabilityData): void
    {
        if (empty($availabilityData)) {
            return;
        }

        foreach ($availabilityData as $dateData) {
            if (!isset($dateData['date'])) {
                continue;
            }

            $date = Carbon::parse($dateData['date'])->format('Y-m-d');

            $data = [
                'status' => $dateData['status'] ?? VacationRentalAvailability::STATUS_AVAILABLE,
                'price_per_night' => isset($dateData['price_per_night']) ? (float) $dateData['price_per_night'] : null,
                'minimum_stay' => isset($dateData['minimum_stay']) ? (int) $dateData['minimum_stay'] : null,
                'notes' => $dateData['notes'] ?? null,
            ];

            VacationRentalAvailability::updateOrCreate([
                'vacation_rental_id' => $vacationRental->id,
                'date' => $date,
            ], $data);
        }
    }

    public function getVacationRentalAvailabilityForForm(VacationRental $vacationRental): array
    {
        if (!$vacationRental->exists) {
            return [
                'availability_by_date' => [],
                'total_records' => 0
            ];
        }

        $availability = $vacationRental->availability()
            ->orderBy('date')
            ->get();

        $availabilityByDate = [];
        foreach ($availability as $item) {
            $availabilityByDate[$item->date->format('Y-m-d')] = [
                'status' => $item->status,
                'price_per_night' => $item->price_per_night,
                'minimum_stay' => $item->minimum_stay,
                'notes' => $item->notes,
                'reason' => $item->notes, // Alias for JavaScript compatibility
                'color' => $item->getStatusColor(),
            ];
        }

        return [
            'availability_by_date' => $availabilityByDate,
            'total_records' => count($availabilityByDate)
        ];
    }

    public function bulkUpdateAvailability(VacationRental $vacationRental, array $dates, array $data): void
    {
        foreach ($dates as $date) {
            VacationRentalAvailability::updateOrCreate([
                'vacation_rental_id' => $vacationRental->id,
                'date' => Carbon::parse($date)->format('Y-m-d'),
            ], $data);
        }
    }

    public function blockDates(VacationRental $vacationRental, array $dates, ?string $reason = null): void
    {
        $this->bulkUpdateAvailability($vacationRental, $dates, [
            'status' => VacationRentalAvailability::STATUS_BLOCKED,
            'notes' => $reason,
        ]);
    }

    public function unblockDates(VacationRental $vacationRental, array $dates): void
    {
        $this->bulkUpdateAvailability($vacationRental, $dates, [
            'status' => VacationRentalAvailability::STATUS_AVAILABLE,
            'notes' => null,
        ]);
    }

    public function maintenanceDates(VacationRental $vacationRental, array $dates, ?string $reason = null): void
    {
        $this->bulkUpdateAvailability($vacationRental, $dates, [
            'status' => VacationRentalAvailability::STATUS_MAINTENANCE,
            'notes' => $reason,
        ]);
    }

    public function setMaintenanceDates(VacationRental $vacationRental, array $dates, ?string $reason = null): void
    {
        $this->bulkUpdateAvailability($vacationRental, $dates, [
            'status' => VacationRentalAvailability::STATUS_MAINTENANCE,
            'notes' => $reason,
        ]);
    }

    public function getAvailabilityForDateRange(VacationRental $vacationRental, Carbon $startDate, Carbon $endDate): array
    {
        return VacationRentalAvailability::getAvailabilityForDateRange(
            $vacationRental->id,
            $startDate,
            $endDate
        );
    }

    public function getMonthlyAvailabilitySummary(VacationRental $vacationRental, int $year, int $month): array
    {
        return VacationRentalAvailability::getMonthlyAvailabilitySummary(
            $vacationRental->id,
            $year,
            $month
        );
    }

    public function checkAvailability(VacationRental $vacationRental, Carbon $startDate, Carbon $endDate): bool
    {
        return VacationRentalAvailability::checkAvailability(
            $vacationRental->id,
            $startDate,
            $endDate
        );
    }

    public function createMissingAvailabilityRecords(VacationRental $vacationRental, Carbon $startDate, Carbon $endDate): void
    {
        VacationRentalAvailability::createMissingAvailabilityRecords(
            $vacationRental->id,
            $startDate,
            $endDate
        );
    }

    public function getAvailabilityCalendarData(VacationRental $vacationRental, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?: Carbon::now()->startOfMonth();
        $endDate = $endDate ?: Carbon::now()->addMonths(12)->endOfMonth();

        $availability = $this->getAvailabilityForDateRange($vacationRental, $startDate, $endDate);
        $events = $vacationRental->calendarEvents()
            ->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->map(fn($event) => $event->toFullCalendarEvent());

        return [
            'availability' => $availability,
            'events' => $events->toArray(),
            'vacation_rental' => [
                'id' => $vacationRental->id,
                'name' => $vacationRental->name,
                'price' => $vacationRental->price,
                'minimum_stay' => $vacationRental->minimum_stay,
                'maximum_stay' => $vacationRental->maximum_stay,
                'maximum_guests' => $vacationRental->maximum_guests,
                'check_in_time' => $vacationRental->check_in_time,
                'check_out_time' => $vacationRental->check_out_time,
            ],
        ];
    }

    /**
     * Get detailed availability information for a date range
     */
    public function getAvailabilityDetails(VacationRental $vacationRental, Carbon $startDate, Carbon $endDate): array
    {
        $availability = VacationRentalAvailability::getAvailabilityForDateRange($vacationRental->id, $startDate, $endDate);

        // Apply availability rules to enhance the data
        foreach ($availability as $date => &$dayInfo) {
            $carbonDate = Carbon::parse($date);

            // Apply pricing rules
            $basePrice = $vacationRental->price ?? 0;
            $effectivePrice = $basePrice; // For now, use base price - can be enhanced with rules later

            // Apply minimum stay rules
            $defaultMinStay = $vacationRental->minimum_stay ?? 1;
            $effectiveMinStay = $defaultMinStay; // For now, use default - can be enhanced with rules later

            // Update with calculated values
            if (!isset($dayInfo['price']) || !$dayInfo['price']) {
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
    public function getAvailabilityExceptions(VacationRental $vacationRental, Carbon $startDate, Carbon $endDate): array
    {
        // Get only non-available dates from the database
        $exceptions = VacationRentalAvailability::where('vacation_rental_id', $vacationRental->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', [
                VacationRentalAvailability::STATUS_BOOKED,
                VacationRentalAvailability::STATUS_BLOCKED,
                VacationRentalAvailability::STATUS_MAINTENANCE
            ])
            ->orderBy('date')
            ->get();

        $result = [];

        foreach ($exceptions as $exception) {
            $carbonDate = Carbon::parse($exception->date);
            $dateKey = $carbonDate->format('Y-m-d');

            // Apply pricing rules
            $basePrice = $vacationRental->price ?? 0;
            $effectivePrice = $basePrice; // For now, use base price

            // Apply minimum stay rules
            $defaultMinStay = $vacationRental->minimum_stay ?? 1;
            $effectiveMinStay = $defaultMinStay; // For now, use default

            // Calculate the actual price for this date
            $actualPrice = $exception->price_per_night ?: $effectivePrice;

            $result[$dateKey] = [
                'date' => $dateKey,
                'status' => $exception->status,
                'price' => $actualPrice,
                'minimum_stay' => $exception->minimum_stay ?: $effectiveMinStay,
                'color' => $this->getStatusColor($exception->status),
                'notes' => $exception->notes,
                // Only include base_price and price_modifier if they're different from the actual price
                // This eliminates duplicate price fields when they're the same
                ...(($actualPrice != $basePrice) ? [
                    'base_price' => $basePrice,
                    'price_modifier' => $basePrice > 0 ? ($actualPrice / $basePrice) : 1,
                ] : [])
            ];
        }

        return $result;
    }

    /**
     * Calculate pricing for a booking
     */
    public function calculateBookingPrice(VacationRental $vacationRental, Carbon $checkIn, Carbon $checkOut, int $guests = 1): array
    {

        $nights = $checkIn->diffInDays($checkOut);

        if ($nights <= 0) {
            throw new \InvalidArgumentException('Check-out date must be after check-in date');
        }

        // Validate guest count against property maximum
        if ($vacationRental->maximum_guests && $guests > $vacationRental->maximum_guests) {
            throw new \InvalidArgumentException("Number of guests ({$guests}) exceeds property maximum ({$vacationRental->maximum_guests})");
        }

        $totalNightsCost = 0;
        $priceBreakdown = [];

        // Calculate nightly rates (use copy to avoid mutating original dates)
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
        foreach ($period as $date) {
            $basePrice = $vacationRental->price ?? 0;
            $nightPrice = $basePrice; // For now, use base price - can be enhanced with rules later

            // Check for specific date overrides
            $availability = VacationRentalAvailability::where('vacation_rental_id', $vacationRental->id)
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
        $cleaningFee = $vacationRental->cleaning_fee ?? 0;
        $securityDeposit = $vacationRental->security_deposit ?? 0;
        $serviceFee = $totalNightsCost * 0.03; // 3% service fee
        $taxes = ($totalNightsCost + $serviceFee) * 0.12; // 12% taxes

        $totalAmount = $totalNightsCost + $cleaningFee + $serviceFee + $taxes;

        return [
            'nights' => $nights,
            'guests' => $guests,
            'base_price_per_night' => $vacationRental->price ?? 0,
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
     * Get status color for calendar display
     */
    private function getStatusColor(string $status): string
    {
        return match ($status) {
            VacationRentalAvailability::STATUS_AVAILABLE => '#28a745',
            VacationRentalAvailability::STATUS_BOOKED => '#007bff',
            VacationRentalAvailability::STATUS_BLOCKED => '#dc3545',
            VacationRentalAvailability::STATUS_MAINTENANCE => '#ffc107',
            default => '#6c757d',
        };
    }
}

<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\PropertyAvailability;
use Botble\RealEstate\Models\PropertyCalendarEvent;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SavePropertyAvailabilityService
{
    public function execute(Property $property, array $availabilityData = []): void
    {
        // Only process availability data for vacation rentals
        if ($property->type !== PropertyTypeEnum::VACATION_RENTAL) {
            return;
        }

        // If no availability data provided, skip processing
        if (empty($availabilityData)) {
            return;
        }

        // Process blocked dates
        if (!empty($availabilityData['blocked_dates'])) {
            $this->processBlockedDates($property->id, $availabilityData['blocked_dates']);
        }

        // Process maintenance dates
        if (!empty($availabilityData['maintenance_dates'])) {
            $this->processMaintenanceDates($property->id, $availabilityData['maintenance_dates']);
        }

        // Process unblocked dates (dates to make available again)
        if (!empty($availabilityData['unblocked_dates'])) {
            $this->processUnblockedDates($property->id, $availabilityData['unblocked_dates']);
        }

        // Process custom pricing for specific dates
        if (!empty($availabilityData['custom_pricing'])) {
            $this->processCustomPricing($property->id, $availabilityData['custom_pricing']);
        }
    }

    private function processBlockedDates(int $propertyId, array $blockedDates): void
    {
        foreach ($blockedDates as $dateRange) {
            if (empty($dateRange['start_date']) || empty($dateRange['end_date'])) {
                continue;
            }

            $startDate = Carbon::parse($dateRange['start_date']);
            $endDate = Carbon::parse($dateRange['end_date']);
            $reason = $dateRange['reason'] ?? 'Blocked by owner';

            // Create calendar event
            PropertyCalendarEvent::createBlockedPeriod($propertyId, $startDate, $endDate, 'Blocked', $reason);

            // Update availability records
            $this->updateAvailabilityRecords($propertyId, $startDate, $endDate, [
                'status' => PropertyAvailability::STATUS_BLOCKED,
                'notes' => $reason,
            ]);
        }
    }

    private function processMaintenanceDates(int $propertyId, array $maintenanceDates): void
    {
        foreach ($maintenanceDates as $dateRange) {
            if (empty($dateRange['start_date']) || empty($dateRange['end_date'])) {
                continue;
            }

            $startDate = Carbon::parse($dateRange['start_date']);
            $endDate = Carbon::parse($dateRange['end_date']);
            $reason = $dateRange['reason'] ?? 'Maintenance';

            // Create calendar event
            PropertyCalendarEvent::createMaintenancePeriod($propertyId, $startDate, $endDate, 'Maintenance', $reason);

            // Update availability records
            $this->updateAvailabilityRecords($propertyId, $startDate, $endDate, [
                'status' => PropertyAvailability::STATUS_MAINTENANCE,
                'notes' => $reason,
            ]);
        }
    }

    private function processUnblockedDates(int $propertyId, array $unblockedDates): void
    {
        foreach ($unblockedDates as $dateRange) {
            if (empty($dateRange['start_date']) || empty($dateRange['end_date'])) {
                continue;
            }

            $startDate = Carbon::parse($dateRange['start_date']);
            $endDate = Carbon::parse($dateRange['end_date']);

            // Remove calendar events
            PropertyCalendarEvent::forProperty($propertyId)
                ->whereIn('event_type', [PropertyCalendarEvent::TYPE_BLOCKED, PropertyCalendarEvent::TYPE_MAINTENANCE])
                ->inDateRange($startDate, $endDate)
                ->delete();

            // Update availability records
            $this->updateAvailabilityRecords($propertyId, $startDate, $endDate, [
                'status' => PropertyAvailability::STATUS_AVAILABLE,
                'notes' => null,
            ]);
        }
    }

    private function processCustomPricing(int $propertyId, array $customPricing): void
    {
        foreach ($customPricing as $priceData) {
            if (empty($priceData['date']) || empty($priceData['price'])) {
                continue;
            }

            $date = Carbon::parse($priceData['date']);
            $price = (float) $priceData['price'];

            PropertyAvailability::updateOrCreate(
                [
                    'property_id' => $propertyId,
                    'date' => $date->format('Y-m-d'),
                ],
                [
                    'price_per_night' => $price,
                    'status' => PropertyAvailability::STATUS_AVAILABLE,
                ]
            );
        }
    }

    private function updateAvailabilityRecords(int $propertyId, Carbon $startDate, Carbon $endDate, array $data): void
    {
        $dates = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        PropertyAvailability::bulkUpdateAvailability($propertyId, $dates, $data);
    }

    /**
     * Clear all availability data for a property
     */
    public function clearPropertyAvailability(int $propertyId): void
    {
        // Remove all availability records
        PropertyAvailability::where('property_id', $propertyId)->delete();
        
        // Remove all calendar events
        PropertyCalendarEvent::where('property_id', $propertyId)->delete();
    }

    /**
     * Get current availability data for a property in form-friendly format
     */
    public function getPropertyAvailabilityForForm(Property $property): array
    {
        if ($property->type !== PropertyTypeEnum::VACATION_RENTAL) {
            return [];
        }

        $blockedEvents = PropertyCalendarEvent::forProperty($property->id)
            ->ofType(PropertyCalendarEvent::TYPE_BLOCKED)
            ->get();

        $maintenanceEvents = PropertyCalendarEvent::forProperty($property->id)
            ->ofType(PropertyCalendarEvent::TYPE_MAINTENANCE)
            ->get();

        $customPricing = PropertyAvailability::forProperty($property->id)
            ->whereNotNull('price_per_night')
            ->where('price_per_night', '>', 0)
            ->get();

        return [
            'blocked_dates' => $blockedEvents->map(function ($event) {
                return [
                    'start_date' => $event->start_date->format('Y-m-d'),
                    'end_date' => $event->end_date->format('Y-m-d'),
                    'reason' => $event->description,
                ];
            })->toArray(),
            'maintenance_dates' => $maintenanceEvents->map(function ($event) {
                return [
                    'start_date' => $event->start_date->format('Y-m-d'),
                    'end_date' => $event->end_date->format('Y-m-d'),
                    'reason' => $event->description,
                ];
            })->toArray(),
            'custom_pricing' => $customPricing->map(function ($availability) {
                return [
                    'date' => $availability->date->format('Y-m-d'),
                    'price' => $availability->price_per_night,
                ];
            })->toArray(),
        ];
    }
}

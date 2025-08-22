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
        // This service is no longer used for properties since vacation rentals have been separated
        // All availability management is now handled by the VacationRental system
        return;

        // If no availability data provided, skip processing
        if (empty($availabilityData)) {
            return;
        }

        // Decode JSON strings if necessary (JavaScript sends JSON strings)
        $processedData = $this->processAvailabilityData($availabilityData);

        \Log::info('Processed availability data', [
            'original_data' => $availabilityData,
            'processed_data' => $processedData,
            'data_types' => array_map('gettype', $processedData)
        ]);

        // Process blocked dates (individual dates from admin)
        if (!empty($processedData['blocked_dates'])) {
            $this->processIndividualBlockedDates($property->id, $processedData['blocked_dates']);
        }

        // Process maintenance dates (individual dates from admin)
        if (!empty($processedData['maintenance_dates'])) {
            $this->processIndividualMaintenanceDates($property->id, $processedData['maintenance_dates']);
        }

        // Process unblocked dates (individual dates to make available again)
        if (!empty($processedData['unblocked_dates'])) {
            $this->processIndividualUnblockedDates($property->id, $processedData['unblocked_dates']);
        }

        // Process custom pricing for specific dates
        if (!empty($processedData['custom_pricing'])) {
            $this->processCustomPricing($property->id, $processedData['custom_pricing']);
        }
    }

    /**
     * Process and decode availability data from form submission
     * JavaScript sends JSON strings, we need to decode them to arrays
     */
    private function processAvailabilityData(array $availabilityData): array
    {
        $processed = [];

        foreach ($availabilityData as $key => $value) {
            if (is_string($value) && $this->isJsonString($value)) {
                // Decode JSON string
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $processed[$key] = $decoded;
                } else {
                    \Log::warning("Failed to decode JSON for {$key}", [
                        'value' => $value,
                        'error' => json_last_error_msg()
                    ]);
                    $processed[$key] = $value;
                }
            } else {
                // Keep as is if not a JSON string
                $processed[$key] = $value;
                \Log::info("Keeping original value for {$key}", [
                    'value' => $value,
                    'type' => gettype($value)
                ]);
            }
        }

        return $processed;
    }

    /**
     * Check if a string is valid JSON
     */
    private function isJsonString(string $string): bool
    {
        if (empty($string) || !is_string($string)) {
            return false;
        }

        // Quick check for JSON-like structure
        $trimmed = trim($string);
        return (
            (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) ||
            (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}'))
        );
    }

    /**
     * Process individual blocked dates from admin availability management
     */
    private function processIndividualBlockedDates(int $propertyId, array $blockedDates): void
    {
        foreach ($blockedDates as $dateString) {
            if (empty($dateString)) {
                continue;
            }

            try {
                $date = Carbon::parse($dateString);
                $reason = 'Blocked by owner';

                // Create calendar event for single day
                PropertyCalendarEvent::createBlockedPeriod($propertyId, $date, $date, 'Blocked', $reason);

                // Update availability record for single day
                $this->updateAvailabilityRecords($propertyId, $date, $date, [
                    'status' => PropertyAvailability::STATUS_BLOCKED,
                    'notes' => $reason,
                ]);

            } catch (\Exception $e) {
                \Log::error('Error processing blocked date', [
                    'property_id' => $propertyId,
                    'date_string' => $dateString,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Process individual maintenance dates from admin availability management
     */
    private function processIndividualMaintenanceDates(int $propertyId, array $maintenanceDates): void
    {
        foreach ($maintenanceDates as $dateString) {
            if (empty($dateString)) {
                continue;
            }

            try {
                $date = Carbon::parse($dateString);
                $reason = 'Maintenance scheduled';

                // Create calendar event for single day
                PropertyCalendarEvent::createMaintenancePeriod($propertyId, $date, $date, 'Maintenance', $reason);

                // Update availability record for single day
                $this->updateAvailabilityRecords($propertyId, $date, $date, [
                    'status' => PropertyAvailability::STATUS_MAINTENANCE,
                    'notes' => $reason,
                ]);

            } catch (\Exception $e) {
                \Log::error('Error processing maintenance date', [
                    'property_id' => $propertyId,
                    'date_string' => $dateString,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Process individual unblocked dates (make available again)
     */
    private function processIndividualUnblockedDates(int $propertyId, array $unblockedDates): void
    {
        foreach ($unblockedDates as $dateString) {
            if (empty($dateString)) {
                continue;
            }

            try {
                $date = Carbon::parse($dateString);

                // Remove any existing calendar events for this date
                PropertyCalendarEvent::where('property_id', $propertyId)
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->delete();

                // Update availability record to make available
                $this->updateAvailabilityRecords($propertyId, $date, $date, [
                    'status' => PropertyAvailability::STATUS_AVAILABLE,
                    'notes' => 'Made available by owner',
                ]);

            } catch (\Exception $e) {
                \Log::error('Error processing unblocked date', [
                    'property_id' => $propertyId,
                    'date_string' => $dateString,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Process blocked date ranges (for booking system)
     */
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
        \Log::info('getPropertyAvailabilityForForm called', [
            'property_id' => $property->id,
            'property_type' => $property->type,
            'expected_type' => PropertyTypeEnum::VACATION_RENTAL,
            'types_match' => $property->type == PropertyTypeEnum::VACATION_RENTAL
        ]);

        if ($property->type != PropertyTypeEnum::VACATION_RENTAL) {
            \Log::info('Property type mismatch, returning empty array');
            return [];
        }

        // Get availability records directly (individual dates approach)
        $availabilityRecords = PropertyAvailability::where('property_id', $property->id)->get();

        $blockedDates = [];
        $maintenanceDates = [];
        $unblockedDates = [];
        $customPricing = [];
        $availabilityByDate = [];

        foreach ($availabilityRecords as $record) {
            $dateString = $record->date->format('Y-m-d');

            // Build availability by date for calendar display
            $availabilityByDate[$dateString] = [
                'status' => $record->status,
                'reason' => $record->notes,
                'price' => $record->price_per_night,
            ];

            // Group by status for form data (individual dates)
            switch ($record->status) {
                case PropertyAvailability::STATUS_BLOCKED:
                    $blockedDates[] = $dateString;
                    break;
                case PropertyAvailability::STATUS_MAINTENANCE:
                    $maintenanceDates[] = $dateString;
                    break;
                case PropertyAvailability::STATUS_AVAILABLE:
                    // Only include explicitly unblocked dates (not default available)
                    if ($record->notes === 'Made available by owner') {
                        $unblockedDates[] = $dateString;
                    }
                    break;
            }

            // Custom pricing
            if ($record->price_per_night && $record->price_per_night > 0) {
                $customPricing[] = [
                    'date' => $dateString,
                    'price' => $record->price_per_night,
                ];
            }
        }

        $result = [
            'blocked_dates' => $blockedDates,
            'maintenance_dates' => $maintenanceDates,
            'unblocked_dates' => $unblockedDates,
            'custom_pricing' => $customPricing,
            'availability_by_date' => $availabilityByDate,
        ];

        \Log::info('getPropertyAvailabilityForForm result (individual dates format)', [
            'blocked_dates_count' => count($blockedDates),
            'maintenance_dates_count' => count($maintenanceDates),
            'unblocked_dates_count' => count($unblockedDates),
            'availability_records_count' => count($availabilityByDate),
            'blocked_dates' => $blockedDates,
            'maintenance_dates' => $maintenanceDates,
            'unblocked_dates' => $unblockedDates
        ]);

        return $result;
    }
}

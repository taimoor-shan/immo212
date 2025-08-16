<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\VacationRental;
use Botble\RealEstate\Models\VacationRentalAvailability;
use Botble\RealEstate\Models\VacationRentalCalendarEvent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SaveVacationRentalAvailabilityService
{
    public function execute(VacationRental $vacationRental, array $availabilityData = []): void
    {
        // If no availability data provided, skip processing
        if (empty($availabilityData)) {
            return;
        }

        // Decode JSON strings if necessary (JavaScript sends JSON strings)
        $processedData = $this->processAvailabilityData($availabilityData);

        \Log::info('Processed vacation rental availability data', [
            'vacation_rental_id' => $vacationRental->id,
            'original_data' => $availabilityData,
            'processed_data' => $processedData,
            'data_types' => array_map('gettype', $processedData)
        ]);

        // Process blocked dates (individual dates from admin)
        if (!empty($processedData['blocked_dates'])) {
            $this->processIndividualBlockedDates($vacationRental->id, $processedData['blocked_dates']);
        }

        // Process maintenance dates (individual dates from admin)
        if (!empty($processedData['maintenance_dates'])) {
            $this->processIndividualMaintenanceDates($vacationRental->id, $processedData['maintenance_dates']);
        }

        // Process unblocked dates (individual dates to make available again)
        if (!empty($processedData['unblocked_dates'])) {
            $this->processIndividualUnblockedDates($vacationRental->id, $processedData['unblocked_dates']);
        }

        // Process custom pricing for specific dates
        if (!empty($processedData['custom_pricing'])) {
            $this->processCustomPricing($vacationRental->id, $processedData['custom_pricing']);
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
    private function processIndividualBlockedDates(int $vacationRentalId, array $blockedDates): void
    {
        foreach ($blockedDates as $dateString) {
            if (empty($dateString)) {
                continue;
            }

            try {
                $date = Carbon::parse($dateString);
                $reason = 'Blocked by owner';

                // Create calendar event for single day
                VacationRentalCalendarEvent::createBlockedPeriod($vacationRentalId, $date, $date, 'Blocked', $reason);

                // Update availability record for single day
                $this->updateAvailabilityRecords($vacationRentalId, $date, $date, [
                    'status' => VacationRentalAvailability::STATUS_BLOCKED,
                    'reason' => $reason,
                ]);

            } catch (\Exception $e) {
                \Log::error('Error processing blocked date', [
                    'vacation_rental_id' => $vacationRentalId,
                    'date_string' => $dateString,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Process individual maintenance dates from admin availability management
     */
    private function processIndividualMaintenanceDates(int $vacationRentalId, array $maintenanceDates): void
    {
        foreach ($maintenanceDates as $dateString) {
            if (empty($dateString)) {
                continue;
            }

            try {
                $date = Carbon::parse($dateString);
                $reason = 'Maintenance scheduled';

                // Create calendar event for single day
                VacationRentalCalendarEvent::createMaintenancePeriod($vacationRentalId, $date, $date, 'Maintenance', $reason);

                // Update availability record for single day
                $this->updateAvailabilityRecords($vacationRentalId, $date, $date, [
                    'status' => VacationRentalAvailability::STATUS_MAINTENANCE,
                    'reason' => $reason,
                ]);

            } catch (\Exception $e) {
                \Log::error('Error processing maintenance date', [
                    'vacation_rental_id' => $vacationRentalId,
                    'date_string' => $dateString,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Process individual unblocked dates (make available again)
     */
    private function processIndividualUnblockedDates(int $vacationRentalId, array $unblockedDates): void
    {
        foreach ($unblockedDates as $dateString) {
            if (empty($dateString)) {
                continue;
            }

            try {
                $date = Carbon::parse($dateString);

                // Remove any existing calendar events for this date
                VacationRentalCalendarEvent::where('vacation_rental_id', $vacationRentalId)
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->delete();

                // Update availability record to make available
                $this->updateAvailabilityRecords($vacationRentalId, $date, $date, [
                    'status' => VacationRentalAvailability::STATUS_AVAILABLE,
                    'reason' => 'Made available by owner',
                ]);

            } catch (\Exception $e) {
                \Log::error('Error processing unblocked date', [
                    'vacation_rental_id' => $vacationRentalId,
                    'date_string' => $dateString,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Process blocked date ranges (for booking system)
     */
    private function processBlockedDates(int $vacationRentalId, array $blockedDates): void
    {
        foreach ($blockedDates as $dateRange) {
            if (empty($dateRange['start_date']) || empty($dateRange['end_date'])) {
                continue;
            }

            $startDate = Carbon::parse($dateRange['start_date']);
            $endDate = Carbon::parse($dateRange['end_date']);
            $reason = $dateRange['reason'] ?? 'Blocked by owner';

            // Create calendar event
            VacationRentalCalendarEvent::createBlockedPeriod($vacationRentalId, $startDate, $endDate, 'Blocked', $reason);

            // Update availability records
            $this->updateAvailabilityRecords($vacationRentalId, $startDate, $endDate, [
                'status' => VacationRentalAvailability::STATUS_BLOCKED,
                'reason' => $reason,
            ]);
        }
    }

    private function processMaintenanceDates(int $vacationRentalId, array $maintenanceDates): void
    {
        foreach ($maintenanceDates as $dateRange) {
            if (empty($dateRange['start_date']) || empty($dateRange['end_date'])) {
                continue;
            }

            $startDate = Carbon::parse($dateRange['start_date']);
            $endDate = Carbon::parse($dateRange['end_date']);
            $reason = $dateRange['reason'] ?? 'Maintenance';

            // Create calendar event
            VacationRentalCalendarEvent::createMaintenancePeriod($vacationRentalId, $startDate, $endDate, 'Maintenance', $reason);

            // Update availability records
            $this->updateAvailabilityRecords($vacationRentalId, $startDate, $endDate, [
                'status' => VacationRentalAvailability::STATUS_MAINTENANCE,
                'reason' => $reason,
            ]);
        }
    }

    private function processUnblockedDates(int $vacationRentalId, array $unblockedDates): void
    {
        foreach ($unblockedDates as $dateRange) {
            if (empty($dateRange['start_date']) || empty($dateRange['end_date'])) {
                continue;
            }

            $startDate = Carbon::parse($dateRange['start_date']);
            $endDate = Carbon::parse($dateRange['end_date']);

            // Remove calendar events
            VacationRentalCalendarEvent::forVacationRental($vacationRentalId)
                ->whereIn('event_type', [VacationRentalCalendarEvent::TYPE_BLOCKED, VacationRentalCalendarEvent::TYPE_MAINTENANCE])
                ->inDateRange($startDate, $endDate)
                ->delete();

            // Update availability records
            $this->updateAvailabilityRecords($vacationRentalId, $startDate, $endDate, [
                'status' => VacationRentalAvailability::STATUS_AVAILABLE,
                'reason' => null,
            ]);
        }
    }

    private function processCustomPricing(int $vacationRentalId, array $customPricing): void
    {
        foreach ($customPricing as $priceData) {
            if (empty($priceData['date']) || empty($priceData['price'])) {
                continue;
            }

            $date = Carbon::parse($priceData['date']);
            $price = (float) $priceData['price'];

            VacationRentalAvailability::updateOrCreate(
                [
                    'vacation_rental_id' => $vacationRentalId,
                    'date' => $date->format('Y-m-d'),
                ],
                [
                    'price' => $price,
                    'status' => VacationRentalAvailability::STATUS_AVAILABLE,
                ]
            );
        }
    }

    private function updateAvailabilityRecords(int $vacationRentalId, Carbon $startDate, Carbon $endDate, array $data): void
    {
        $dates = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        VacationRentalAvailability::bulkUpdateAvailability($vacationRentalId, $dates, $data);
    }

    /**
     * Clear all availability data for a vacation rental
     */
    public function clearVacationRentalAvailability(int $vacationRentalId): void
    {
        // Remove all availability records
        VacationRentalAvailability::where('vacation_rental_id', $vacationRentalId)->delete();

        // Remove all calendar events
        VacationRentalCalendarEvent::where('vacation_rental_id', $vacationRentalId)->delete();
    }

    /**
     * Get current availability data for a vacation rental in form-friendly format
     */
    public function getVacationRentalAvailabilityForForm(VacationRental $vacationRental): array
    {
        $vacationRentalId = $vacationRental->id;
        
        \Log::info('getVacationRentalAvailabilityForForm called', [
            'vacation_rental_id' => $vacationRentalId
        ]);
        
        // Get availability records for vacation rental
        $availabilityRecords = VacationRentalAvailability::where('vacation_rental_id', $vacationRentalId)->get();

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
                'reason' => $record->reason,
                'price' => $record->price,
            ];

            // Group by status for form data (individual dates)
            switch ($record->status) {
                case VacationRentalAvailability::STATUS_BLOCKED:
                    $blockedDates[] = $dateString;
                    break;
                case VacationRentalAvailability::STATUS_MAINTENANCE:
                    $maintenanceDates[] = $dateString;
                    break;
                case VacationRentalAvailability::STATUS_AVAILABLE:
                    // Only include explicitly unblocked dates (not default available)
                    if ($record->reason === 'Made available by owner') {
                        $unblockedDates[] = $dateString;
                    }
                    break;
            }

            // Custom pricing
            if ($record->price && $record->price > 0) {
                $customPricing[] = [
                    'date' => $dateString,
                    'price' => $record->price,
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

        \Log::info('getVacationRentalAvailabilityForForm result', [
            'blocked_dates_count' => count($blockedDates),
            'maintenance_dates_count' => count($maintenanceDates),
            'unblocked_dates_count' => count($unblockedDates),
            'availability_records_count' => count($availabilityByDate)
        ]);

        return $result;
    }
}

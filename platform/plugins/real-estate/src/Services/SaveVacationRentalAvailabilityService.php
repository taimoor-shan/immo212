<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\VacationRental;
use Botble\RealEstate\Models\VacationRentalAvailability;
use Carbon\Carbon;

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
            return [];
        }

        $availability = $vacationRental->availability()
            ->orderBy('date')
            ->get();

        $result = [];
        foreach ($availability as $item) {
            $result[] = [
                'date' => $item->date->format('Y-m-d'),
                'status' => $item->status,
                'price_per_night' => $item->price_per_night,
                'minimum_stay' => $item->minimum_stay,
                'notes' => $item->notes,
                'color' => $item->getStatusColor(),
            ];
        }

        return $result;
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
}

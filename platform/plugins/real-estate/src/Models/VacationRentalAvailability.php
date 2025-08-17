<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class VacationRentalAvailability extends BaseModel
{
    protected $table = 're_vacation_rental_availability';

    protected $fillable = [
        'vacation_rental_id',
        'date',
        'status',
        'price_per_night',
        'minimum_stay',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'price_per_night' => 'float',
        'minimum_stay' => 'int',
    ];

    // Status constants
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_BOOKED = 'booked';
    public const STATUS_MAINTENANCE = 'maintenance';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_BLOCKED => 'Blocked',
            self::STATUS_BOOKED => 'Booked',
            self::STATUS_MAINTENANCE => 'Maintenance',
        ];
    }

    // Relationships
    public function vacationRental(): BelongsTo
    {
        return $this->belongsTo(VacationRental::class);
    }

    // Scopes
    public function scopeForVacationRental(Builder $query, int $vacationRentalId): Builder
    {
        return $query->where('vacation_rental_id', $vacationRentalId);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeBooked(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_BOOKED);
    }

    public function scopeBlocked(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_BLOCKED);
    }

    public function scopeInDateRange(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
    }

    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        return $query->inDateRange($startDate, $endDate);
    }

    // Helper methods
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isBooked(): bool
    {
        return $this->status === self::STATUS_BOOKED;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    public function getEffectivePrice(): float
    {
        return $this->price_per_night ?? $this->vacationRental->price ?? 0;
    }

    public function getEffectiveMinimumStay(): int
    {
        return $this->minimum_stay ?? $this->vacationRental->minimum_stay ?? 1;
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE => '#28a745',
            self::STATUS_BOOKED => '#dc3545',
            self::STATUS_BLOCKED => '#ffc107',
            self::STATUS_MAINTENANCE => '#6c757d',
            default => '#007bff',
        };
    }

    public function getStatusLabel(): string
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    // Static helper methods
    public static function checkAvailability(int $vacationRentalId, Carbon $startDate, Carbon $endDate): bool
    {
        // Check if there are any explicitly unavailable dates
        $unavailableDates = self::forVacationRental($vacationRentalId)
            ->inDateRange($startDate, $endDate)
            ->whereIn('status', [self::STATUS_BOOKED, self::STATUS_BLOCKED, self::STATUS_MAINTENANCE])
            ->count();

        // If there are unavailable dates, return false
        if ($unavailableDates > 0) {
            return false;
        }

        // Get vacation rental to check configuration
        $vacationRental = VacationRental::find($vacationRentalId);
        if (!$vacationRental) {
            return false;
        }

        // For vacation rentals, ensure all dates have explicit availability status
        $totalDays = $startDate->diffInDays($endDate);
        $existingRecords = self::forVacationRental($vacationRentalId)
            ->inDateRange($startDate, $endDate)
            ->count();

        // If not all dates have records, create them as available
        if ($existingRecords < $totalDays) {
            self::createMissingAvailabilityRecords($vacationRentalId, $startDate, $endDate);
        }

        return true;
    }

    public static function createMissingAvailabilityRecords(int $vacationRentalId, Carbon $startDate, Carbon $endDate): void
    {
        $currentDate = $startDate->copy();
        
        while ($currentDate->lt($endDate)) {
            self::firstOrCreate([
                'vacation_rental_id' => $vacationRentalId,
                'date' => $currentDate->format('Y-m-d'),
            ], [
                'status' => self::STATUS_AVAILABLE,
            ]);
            
            $currentDate->addDay();
        }
    }

    public static function bulkUpdateAvailability(int $vacationRentalId, array $dates, array $data): void
    {
        foreach ($dates as $date) {
            self::updateOrCreate([
                'vacation_rental_id' => $vacationRentalId,
                'date' => $date,
            ], $data);
        }
    }

    public static function getAvailabilityForDateRange(int $vacationRentalId, Carbon $startDate, Carbon $endDate): array
    {
        $availability = self::forVacationRental($vacationRentalId)
            ->inDateRange($startDate, $endDate)
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $result = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $record = $availability->get($dateKey);

            $result[$dateKey] = [
                'date' => $dateKey,
                'status' => $record ? $record->status : self::STATUS_AVAILABLE,
                'price_per_night' => $record ? $record->getEffectivePrice() : null,
                'minimum_stay' => $record ? $record->getEffectiveMinimumStay() : null,
                'notes' => $record ? $record->notes : null,
                'color' => $record ? $record->getStatusColor() : '#28a745',
            ];

            $currentDate->addDay();
        }

        return $result;
    }

    public static function getMonthlyAvailabilitySummary(int $vacationRentalId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $availability = self::forVacationRental($vacationRentalId)
            ->forMonth($year, $month)
            ->get()
            ->groupBy('status');

        return [
            'total_days' => $startDate->daysInMonth,
            'available_days' => $availability->get(self::STATUS_AVAILABLE, collect())->count(),
            'booked_days' => $availability->get(self::STATUS_BOOKED, collect())->count(),
            'blocked_days' => $availability->get(self::STATUS_BLOCKED, collect())->count(),
            'maintenance_days' => $availability->get(self::STATUS_MAINTENANCE, collect())->count(),
            'revenue' => $availability->get(self::STATUS_BOOKED, collect())->sum('price_per_night'),
        ];
    }
}

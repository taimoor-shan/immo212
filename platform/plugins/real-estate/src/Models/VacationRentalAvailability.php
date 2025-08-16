<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacationRentalAvailability extends BaseModel
{
    protected $table = 're_vacation_rental_availability';

    protected $fillable = [
        'vacation_rental_id',
        'date',
        'status',
        'price',
        'notes',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'float',
    ];

    // Status constants
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_BOOKED = 'booked';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_MAINTENANCE = 'maintenance';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_BOOKED => 'Booked',
            self::STATUS_BLOCKED => 'Blocked',
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

    public function scopeForDate(Builder $query, Carbon $date): Builder
    {
        return $query->where('date', $date->format('Y-m-d'));
    }

    public function scopeFutureDates(Builder $query): Builder
    {
        return $query->where('date', '>=', Carbon::today());
    }

    // Static methods
    public static function isDateAvailable(int $vacationRentalId, Carbon $date): bool
    {
        $availability = self::forVacationRental($vacationRentalId)
            ->forDate($date)
            ->first();

        return !$availability || $availability->status === self::STATUS_AVAILABLE;
    }

    public static function getAvailabilityForDateRange(int $vacationRentalId, Carbon $startDate, Carbon $endDate): array
    {
        $availability = self::forVacationRental($vacationRentalId)
            ->inDateRange($startDate, $endDate)
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

        $dates = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dates[$dateStr] = $availability->get($dateStr) ?: (object) [
                'date' => $currentDate->copy(),
                'status' => self::STATUS_AVAILABLE,
                'price' => null,
                'notes' => null,
                'reason' => null,
            ];
            $currentDate->addDay();
        }

        return $dates;
    }

    public static function bulkUpdateAvailability(int $vacationRentalId, array $dates, array $data): void
    {
        foreach ($dates as $date) {
            self::updateOrCreate(
                [
                    'vacation_rental_id' => $vacationRentalId,
                    'date' => $date,
                ],
                $data
            );
        }
    }

    public static function blockDates(int $vacationRentalId, Carbon $startDate, Carbon $endDate, string $reason = null): void
    {
        $dates = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        self::bulkUpdateAvailability($vacationRentalId, $dates, [
            'status' => self::STATUS_BLOCKED,
            'reason' => $reason,
        ]);
    }

    public static function unblockDates(int $vacationRentalId, Carbon $startDate, Carbon $endDate): void
    {
        $dates = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        self::bulkUpdateAvailability($vacationRentalId, $dates, [
            'status' => self::STATUS_AVAILABLE,
            'reason' => null,
            'notes' => null,
        ]);
    }

    public static function setMaintenanceDates(int $vacationRentalId, Carbon $startDate, Carbon $endDate, string $reason = null): void
    {
        $dates = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        self::bulkUpdateAvailability($vacationRentalId, $dates, [
            'status' => self::STATUS_MAINTENANCE,
            'reason' => $reason,
        ]);
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

    public function isMaintenance(): bool
    {
        return $this->status === self::STATUS_MAINTENANCE;
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
}

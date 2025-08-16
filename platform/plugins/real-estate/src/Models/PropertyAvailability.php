<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PropertyAvailability extends BaseModel
{
    protected $table = 're_property_availability';

    protected $fillable = [
        'property_id',
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

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // Scopes for common queries
    public function scopeForProperty(Builder $query, int $propertyId): Builder
    {
        return $query->where('property_id', $propertyId);
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
        return $this->price_per_night ?? $this->property->price ?? 0;
    }

    public function getEffectiveMinimumStay(): int
    {
        return $this->minimum_stay ?? $this->property->minimum_stay ?? 1;
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
    public static function checkAvailability(int $propertyId, Carbon $startDate, Carbon $endDate): bool
    {
        // Check if there are any explicitly unavailable dates
        $unavailableDates = self::forProperty($propertyId)
            ->inDateRange($startDate, $endDate)
            ->whereIn('status', [self::STATUS_BOOKED, self::STATUS_BLOCKED, self::STATUS_MAINTENANCE])
            ->count();

        // If there are unavailable dates, return false
        if ($unavailableDates > 0) {
            return false;
        }

        // Get property to verify it exists
        $property = Property::find($propertyId);
        if (!$property) {
            return false;
        }

        // For all properties, use consistent availability logic
        // If no availability records exist, assume available
        $totalRecords = self::forProperty($propertyId)
            ->inDateRange($startDate, $endDate)
            ->count();

        if ($totalRecords === 0) {
            return true;
        }

        // If records exist, check that all dates are explicitly available
        $availableDates = self::forProperty($propertyId)
            ->inDateRange($startDate, $endDate)
            ->where('status', self::STATUS_AVAILABLE)
            ->count();

        $expectedDays = $startDate->diffInDays($endDate);
        return $availableDates >= $expectedDays;
    }

    public static function getAvailabilityForDateRange(int $propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $availability = self::forProperty($propertyId)
            ->inDateRange($startDate, $endDate)
            ->orderBy('date')
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

        $result = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $availabilityRecord = $availability->get($dateKey);

            $result[$dateKey] = [
                'date' => $currentDate->format('Y-m-d'),
                'status' => $availabilityRecord?->status ?? self::STATUS_AVAILABLE,
                'price' => $availabilityRecord?->getEffectivePrice() ?? 0,
                'minimum_stay' => $availabilityRecord?->getEffectiveMinimumStay() ?? 1,
                'color' => $availabilityRecord?->getStatusColor() ?? '#28a745',
                'notes' => $availabilityRecord?->notes,
            ];

            $currentDate->addDay();
        }

        return $result;
    }

    public static function bulkUpdateAvailability(int $propertyId, array $dates, array $data): void
    {
        foreach ($dates as $date) {
            self::updateOrCreate(
                [
                    'property_id' => $propertyId,
                    'date' => $date,
                ],
                $data
            );
        }
    }
}

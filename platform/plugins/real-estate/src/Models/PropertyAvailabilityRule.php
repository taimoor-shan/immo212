<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PropertyAvailabilityRule extends BaseModel
{
    protected $table = 're_property_availability_rules';

    protected $fillable = [
        'property_id',
        'name',
        'type',
        'start_date',
        'end_date',
        'days_of_week',
        'price_modifier',
        'fixed_price',
        'minimum_stay_override',
        'is_active',
        'priority',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_of_week' => 'array',
        'price_modifier' => 'float',
        'fixed_price' => 'float',
        'minimum_stay_override' => 'int',
        'is_active' => 'boolean',
        'priority' => 'int',
    ];

    // Rule type constants
    public const TYPE_SEASONAL_PRICING = 'seasonal_pricing';
    public const TYPE_BLOCKED_PERIOD = 'blocked_period';
    public const TYPE_MINIMUM_STAY = 'minimum_stay';
    public const TYPE_SPECIAL_RATE = 'special_rate';

    public static function getTypes(): array
    {
        return [
            self::TYPE_SEASONAL_PRICING => 'Seasonal Pricing',
            self::TYPE_BLOCKED_PERIOD => 'Blocked Period',
            self::TYPE_MINIMUM_STAY => 'Minimum Stay Override',
            self::TYPE_SPECIAL_RATE => 'Special Rate',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // Scopes
    public function scopeForProperty(Builder $query, int $propertyId): Builder
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderBy('priority', 'desc');
    }

    public function scopeForDate(Builder $query, Carbon $date): Builder
    {
        return $query->where('start_date', '<=', $date->format('Y-m-d'))
                    ->where('end_date', '>=', $date->format('Y-m-d'));
    }

    public function scopeForDateRange(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
              ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate->format('Y-m-d'))
                     ->where('end_date', '>=', $endDate->format('Y-m-d'));
              });
        });
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function appliesToDate(Carbon $date): bool
    {
        // Check if date is within range
        if ($date->lt($this->start_date) || $date->gt($this->end_date)) {
            return false;
        }

        // Check day of week if specified
        if ($this->days_of_week && !empty($this->days_of_week)) {
            $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
            return in_array($dayOfWeek, $this->days_of_week);
        }

        return true;
    }

    public function calculatePrice(float $basePrice): float
    {
        if ($this->fixed_price) {
            return $this->fixed_price;
        }

        if ($this->price_modifier) {
            return $basePrice * $this->price_modifier;
        }

        return $basePrice;
    }

    public function getTypeLabel(): string
    {
        return self::getTypes()[$this->type] ?? 'Unknown';
    }

    public function getDaysOfWeekLabels(): array
    {
        if (!$this->days_of_week) {
            return ['All days'];
        }

        $dayLabels = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        return array_map(fn($day) => $dayLabels[$day] ?? 'Unknown', $this->days_of_week);
    }

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date);
    }

    // Static helper methods
    public static function getApplicableRules(int $propertyId, Carbon $date): array
    {
        return self::forProperty($propertyId)
            ->active()
            ->forDate($date)
            ->byPriority()
            ->get()
            ->filter(fn($rule) => $rule->appliesToDate($date))
            ->toArray();
    }

    public static function calculateEffectivePrice(int $propertyId, Carbon $date, float $basePrice): float
    {
        $rules = self::getApplicableRules($propertyId, $date);

        foreach ($rules as $rule) {
            if (in_array($rule['type'], [self::TYPE_SEASONAL_PRICING, self::TYPE_SPECIAL_RATE])) {
                return (new self($rule))->calculatePrice($basePrice);
            }
        }

        return $basePrice;
    }

    public static function getEffectiveMinimumStay(int $propertyId, Carbon $date, int $defaultMinimumStay): int
    {
        $rules = self::getApplicableRules($propertyId, $date);

        foreach ($rules as $rule) {
            if ($rule['type'] === self::TYPE_MINIMUM_STAY && $rule['minimum_stay_override']) {
                return $rule['minimum_stay_override'];
            }
        }

        return $defaultMinimumStay;
    }

    public static function isDateBlocked(int $propertyId, Carbon $date): bool
    {
        $rules = self::getApplicableRules($propertyId, $date);

        foreach ($rules as $rule) {
            if ($rule['type'] === self::TYPE_BLOCKED_PERIOD) {
                return true;
            }
        }

        return false;
    }
}

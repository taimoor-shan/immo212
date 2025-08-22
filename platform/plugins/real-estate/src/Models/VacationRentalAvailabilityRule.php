<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class VacationRentalAvailabilityRule extends BaseModel
{
    protected $table = 're_vacation_rental_availability_rules';

    protected $fillable = [
        'vacation_rental_id',
        'name',
        'description',
        'rule_type',
        'start_date',
        'end_date',
        'days_of_week',
        'price_adjustment',
        'price_adjustment_type',
        'minimum_stay_override',
        'maximum_stay_override',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_of_week' => 'array',
        'price_adjustment' => 'float',
        'minimum_stay_override' => 'int',
        'maximum_stay_override' => 'int',
        'is_active' => 'boolean',
        'priority' => 'int',
    ];

    // Rule type constants
    public const TYPE_SEASONAL = 'seasonal';
    public const TYPE_WEEKLY = 'weekly';
    public const TYPE_SPECIAL_EVENT = 'special_event';
    public const TYPE_MINIMUM_STAY = 'minimum_stay';
    public const TYPE_MAXIMUM_STAY = 'maximum_stay';

    // Price adjustment type constants
    public const ADJUSTMENT_PERCENTAGE = 'percentage';
    public const ADJUSTMENT_FIXED = 'fixed';

    public static function getRuleTypes(): array
    {
        return [
            self::TYPE_SEASONAL => 'Seasonal Pricing',
            self::TYPE_WEEKLY => 'Weekly Pricing',
            self::TYPE_SPECIAL_EVENT => 'Special Event',
            self::TYPE_MINIMUM_STAY => 'Minimum Stay',
            self::TYPE_MAXIMUM_STAY => 'Maximum Stay',
        ];
    }

    public static function getAdjustmentTypes(): array
    {
        return [
            self::ADJUSTMENT_PERCENTAGE => 'Percentage',
            self::ADJUSTMENT_FIXED => 'Fixed Amount',
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('rule_type', $type);
    }

    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderByDesc('priority');
    }

    public function scopeApplicableForDate(Builder $query, Carbon $date): Builder
    {
        return $query->where(function (Builder $q) use ($date) {
            $q->where(function (Builder $q2) use ($date) {
                // Date range rules
                $q2->whereNotNull('start_date')
                   ->whereNotNull('end_date')
                   ->where('start_date', '<=', $date->format('Y-m-d'))
                   ->where('end_date', '>=', $date->format('Y-m-d'));
            })->orWhere(function (Builder $q2) use ($date) {
                // Weekly rules (day of week)
                $q2->where('rule_type', self::TYPE_WEEKLY)
                   ->whereJsonContains('days_of_week', $date->dayOfWeek);
            });
        });
    }

    // Helper methods
    public function isApplicableForDate(Carbon $date): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check date range
        if ($this->start_date && $this->end_date) {
            return $date->between($this->start_date, $this->end_date);
        }

        // Check weekly rules
        if ($this->rule_type === self::TYPE_WEEKLY && $this->days_of_week) {
            return in_array($date->dayOfWeek, $this->days_of_week);
        }

        return false;
    }

    public function calculateAdjustedPrice(float $basePrice): float
    {
        if (!$this->price_adjustment) {
            return $basePrice;
        }

        if ($this->price_adjustment_type === self::ADJUSTMENT_PERCENTAGE) {
            return $basePrice * (1 + ($this->price_adjustment / 100));
        }

        return $basePrice + $this->price_adjustment;
    }

    public function getEffectiveMinimumStay(): ?int
    {
        return $this->minimum_stay_override;
    }

    public function getEffectiveMaximumStay(): ?int
    {
        return $this->maximum_stay_override;
    }

    public function getRuleTypeLabel(): string
    {
        return self::getRuleTypes()[$this->rule_type] ?? 'Unknown';
    }

    public function getAdjustmentTypeLabel(): string
    {
        return self::getAdjustmentTypes()[$this->price_adjustment_type] ?? 'Unknown';
    }

    public function getFormattedPriceAdjustment(): string
    {
        if (!$this->price_adjustment) {
            return 'No adjustment';
        }

        if ($this->price_adjustment_type === self::ADJUSTMENT_PERCENTAGE) {
            $sign = $this->price_adjustment >= 0 ? '+' : '';
            return $sign . number_format($this->price_adjustment, 1) . '%';
        }

        $sign = $this->price_adjustment >= 0 ? '+' : '';
        return $sign . format_price($this->price_adjustment);
    }

    // Static helper methods
    public static function getApplicableRulesForDate(int $vacationRentalId, Carbon $date): array
    {
        return self::forVacationRental($vacationRentalId)
            ->active()
            ->applicableForDate($date)
            ->byPriority()
            ->get()
            ->toArray();
    }

    public static function calculatePriceForDate(int $vacationRentalId, Carbon $date, float $basePrice): array
    {
        $rules = self::getApplicableRulesForDate($vacationRentalId, $date);
        $finalPrice = $basePrice;
        $appliedRules = [];
        $minimumStay = null;
        $maximumStay = null;

        foreach ($rules as $rule) {
            $ruleModel = new self($rule);
            
            // Apply price adjustments
            if ($ruleModel->price_adjustment) {
                $finalPrice = $ruleModel->calculateAdjustedPrice($finalPrice);
                $appliedRules[] = [
                    'name' => $ruleModel->name,
                    'type' => $ruleModel->rule_type,
                    'adjustment' => $ruleModel->getFormattedPriceAdjustment(),
                ];
            }

            // Apply stay restrictions (highest priority wins)
            if ($ruleModel->minimum_stay_override !== null) {
                $minimumStay = $ruleModel->minimum_stay_override;
            }
            
            if ($ruleModel->maximum_stay_override !== null) {
                $maximumStay = $ruleModel->maximum_stay_override;
            }
        }

        return [
            'base_price' => $basePrice,
            'final_price' => $finalPrice,
            'applied_rules' => $appliedRules,
            'minimum_stay' => $minimumStay,
            'maximum_stay' => $maximumStay,
        ];
    }
}

<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PropertyCalendarEvent extends BaseModel
{
    protected $table = 're_property_calendar_events';

    protected $fillable = [
        'property_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'event_type',
        'color',
        'booking_id',
        'is_all_day',
        'start_time',
        'end_time',
        'is_recurring',
        'recurring_pattern',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_all_day' => 'boolean',
        'is_recurring' => 'boolean',
        'recurring_pattern' => 'array',
    ];

    // Event type constants
    public const TYPE_BOOKING = 'booking';
    public const TYPE_BLOCKED = 'blocked';
    public const TYPE_MAINTENANCE = 'maintenance';
    public const TYPE_PERSONAL_USE = 'personal_use';
    public const TYPE_CLEANING = 'cleaning';

    public static function getEventTypes(): array
    {
        return [
            self::TYPE_BOOKING => 'Booking',
            self::TYPE_BLOCKED => 'Blocked',
            self::TYPE_MAINTENANCE => 'Maintenance',
            self::TYPE_PERSONAL_USE => 'Personal Use',
            self::TYPE_CLEANING => 'Cleaning',
        ];
    }

    public static function getEventTypeColors(): array
    {
        return [
            self::TYPE_BOOKING => '#28a745',
            self::TYPE_BLOCKED => '#dc3545',
            self::TYPE_MAINTENANCE => '#6c757d',
            self::TYPE_PERSONAL_USE => '#17a2b8',
            self::TYPE_CLEANING => '#ffc107',
        ];
    }

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(VacationRentalBooking::class, 'booking_id');
    }

    // Scopes
    public function scopeForProperty(Builder $query, int $propertyId): Builder
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeInDateRange(Builder $query, Carbon $startDate, Carbon $endDate): Builder
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

    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        return $query->inDateRange($startDate, $endDate);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('event_type', $type);
    }

    public function scopeBookings(Builder $query): Builder
    {
        return $query->where('event_type', self::TYPE_BOOKING);
    }

    public function scopeBlocked(Builder $query): Builder
    {
        return $query->where('event_type', self::TYPE_BLOCKED);
    }

    // Helper methods
    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function isActive(): bool
    {
        $today = Carbon::today();
        return $this->start_date->lte($today) && $this->end_date->gte($today);
    }

    public function isUpcoming(): bool
    {
        return $this->start_date->isFuture();
    }

    public function isPast(): bool
    {
        return $this->end_date->isPast();
    }

    public function getEventTypeLabel(): string
    {
        return self::getEventTypes()[$this->event_type] ?? 'Unknown';
    }

    public function getDefaultColor(): string
    {
        return self::getEventTypeColors()[$this->event_type] ?? '#007bff';
    }

    public function getEffectiveColor(): string
    {
        return $this->color ?: $this->getDefaultColor();
    }

    // Calendar formatting methods
    public function toFullCalendarEvent(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start_date->format('Y-m-d'),
            'end' => $this->end_date->copy()->addDay()->format('Y-m-d'), // FullCalendar end is exclusive
            'color' => $this->getEffectiveColor(),
            'description' => $this->description,
            'allDay' => $this->is_all_day,
            'extendedProps' => [
                'event_type' => $this->event_type,
                'event_type_label' => $this->getEventTypeLabel(),
                'booking_id' => $this->booking_id,
                'notes' => $this->notes,
                'property_id' => $this->property_id,
            ],
        ];
    }

    public function toCalendarArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'event_type' => $this->event_type,
            'event_type_label' => $this->getEventTypeLabel(),
            'color' => $this->getEffectiveColor(),
            'duration_days' => $this->getDurationInDays(),
            'is_active' => $this->isActive(),
            'is_upcoming' => $this->isUpcoming(),
            'is_past' => $this->isPast(),
            'booking_id' => $this->booking_id,
            'notes' => $this->notes,
        ];
    }

    // Static helper methods
    public static function getEventsForCalendar(int $propertyId, Carbon $startDate, Carbon $endDate): array
    {
        return self::forProperty($propertyId)
            ->inDateRange($startDate, $endDate)
            ->orderBy('start_date')
            ->get()
            ->map(fn($event) => $event->toFullCalendarEvent())
            ->toArray();
    }

    public static function getMonthlyEvents(int $propertyId, int $year, int $month): array
    {
        return self::forProperty($propertyId)
            ->forMonth($year, $month)
            ->orderBy('start_date')
            ->get()
            ->map(fn($event) => $event->toCalendarArray())
            ->toArray();
    }

    public static function createBlockedPeriod(int $propertyId, Carbon $startDate, Carbon $endDate, string $title = 'Blocked', ?string $notes = null): self
    {
        return self::create([
            'property_id' => $propertyId,
            'title' => $title,
            'description' => $notes,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'event_type' => self::TYPE_BLOCKED,
            'color' => self::getEventTypeColors()[self::TYPE_BLOCKED],
            'notes' => $notes,
        ]);
    }

    public static function createMaintenancePeriod(int $propertyId, Carbon $startDate, Carbon $endDate, string $title = 'Maintenance', ?string $notes = null): self
    {
        return self::create([
            'property_id' => $propertyId,
            'title' => $title,
            'description' => $notes,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'event_type' => self::TYPE_MAINTENANCE,
            'color' => self::getEventTypeColors()[self::TYPE_MAINTENANCE],
            'notes' => $notes,
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (!$event->color) {
                $event->color = $event->getDefaultColor();
            }
        });

        static::created(function ($event) {
            // Update property availability when calendar events are created
            if (in_array($event->event_type, [self::TYPE_BLOCKED, self::TYPE_MAINTENANCE])) {
                $status = $event->event_type === self::TYPE_BLOCKED 
                    ? PropertyAvailability::STATUS_BLOCKED 
                    : PropertyAvailability::STATUS_MAINTENANCE;

                PropertyAvailability::bulkUpdateAvailability(
                    $event->property_id,
                    $event->getDateRange(),
                    ['status' => $status]
                );
            }
        });
    }

    private function getDateRange(): array
    {
        $dates = [];
        $currentDate = $this->start_date->copy();
        
        while ($currentDate->lte($this->end_date)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }
        
        return $dates;
    }
}

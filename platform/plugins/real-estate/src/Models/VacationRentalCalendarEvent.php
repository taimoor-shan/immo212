<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class VacationRentalCalendarEvent extends BaseModel
{
    protected $table = 're_vacation_rental_calendar_events';

    protected $fillable = [
        'vacation_rental_id',
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
    public const TYPE_CUSTOM = 'custom';

    public static function getEventTypes(): array
    {
        return [
            self::TYPE_BOOKING => 'Booking',
            self::TYPE_BLOCKED => 'Blocked',
            self::TYPE_MAINTENANCE => 'Maintenance',
            self::TYPE_CUSTOM => 'Custom',
        ];
    }

    // Default colors for event types
    public static function getDefaultColors(): array
    {
        return [
            self::TYPE_BOOKING => '#28a745',
            self::TYPE_BLOCKED => '#ffc107',
            self::TYPE_MAINTENANCE => '#6c757d',
            self::TYPE_CUSTOM => '#007bff',
        ];
    }

    // Relationships
    public function vacationRental(): BelongsTo
    {
        return $this->belongsTo(VacationRental::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(VacationRentalBooking::class, 'booking_id');
    }

    // Scopes
    public function scopeForVacationRental(Builder $query, int $vacationRentalId): Builder
    {
        return $query->where('vacation_rental_id', $vacationRentalId);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('event_type', $type);
    }

    public function scopeInDateRange(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->where(function (Builder $q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
              ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
              ->orWhere(function (Builder $q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate->format('Y-m-d'))
                     ->where('end_date', '>=', $endDate->format('Y-m-d'));
              });
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('end_date', '>=', Carbon::today());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_date', '>', Carbon::today());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('end_date', '<', Carbon::today());
    }

    // Helper methods
    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function isActive(): bool
    {
        $today = Carbon::today();
        return $this->start_date <= $today && $this->end_date >= $today;
    }

    public function isUpcoming(): bool
    {
        return $this->start_date > Carbon::today();
    }

    public function isPast(): bool
    {
        return $this->end_date < Carbon::today();
    }

    public function getEventTypeLabel(): string
    {
        return self::getEventTypes()[$this->event_type] ?? 'Unknown';
    }

    public function getEffectiveColor(): string
    {
        if ($this->color) {
            return $this->color;
        }

        return self::getDefaultColors()[$this->event_type] ?? '#007bff';
    }

    public function overlapsWithDateRange(Carbon $startDate, Carbon $endDate): bool
    {
        return $this->start_date <= $endDate && $this->end_date >= $startDate;
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
                'vacation_rental_id' => $this->vacation_rental_id,
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
    public static function createFromBooking(VacationRentalBooking $booking): self
    {
        return self::create([
            'vacation_rental_id' => $booking->vacation_rental_id,
            'booking_id' => $booking->id,
            'title' => "Booking: {$booking->guest_name}",
            'description' => "Guests: {$booking->guests_count}",
            'start_date' => $booking->check_in_date,
            'end_date' => $booking->check_out_date->copy()->subDay(), // End date is exclusive
            'event_type' => self::TYPE_BOOKING,
            'color' => self::getDefaultColors()[self::TYPE_BOOKING],
        ]);
    }

    public static function getEventsForVacationRental(int $vacationRentalId, Carbon $startDate = null, Carbon $endDate = null): array
    {
        $query = self::forVacationRental($vacationRentalId);

        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }

        return $query->orderBy('start_date')
            ->get()
            ->map(fn ($event) => $event->toFullCalendarEvent())
            ->toArray();
    }

    public static function getUpcomingEventsForVacationRental(int $vacationRentalId, int $limit = 5): array
    {
        return self::forVacationRental($vacationRentalId)
            ->upcoming()
            ->orderBy('start_date')
            ->limit($limit)
            ->get()
            ->map(fn ($event) => $event->toCalendarArray())
            ->toArray();
    }
}

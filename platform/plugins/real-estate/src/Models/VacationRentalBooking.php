<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Botble\RealEstate\Models\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class VacationRentalBooking extends BaseModel
{
    use HasFactory;

    protected $table = 're_vacation_rental_bookings';

    protected $fillable = [
        'booking_number',
        'property_id',
        'account_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_address',
        'check_in_date',
        'check_out_date',
        'nights_count',
        'guests_count',

        'base_price_per_night',
        'total_nights_cost',
        'cleaning_fee',
        'security_deposit',
        'service_fee',
        'taxes',
        'total_amount',
        'status',
        'payment_status',
        'special_requests',
        'cancellation_reason',
        'cancelled_at',
        'internal_notes',
        'confirmation_sent_at',
        'reminder_sent_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'nights_count' => 'int',
        'guests_count' => 'int',

        'base_price_per_night' => 'float',
        'total_nights_cost' => 'float',
        'cleaning_fee' => 'float',
        'security_deposit' => 'float',
        'service_fee' => 'float',
        'taxes' => 'float',
        'total_amount' => 'float',
        'cancelled_at' => 'datetime',
        'confirmation_sent_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_NO_SHOW = 'no_show';

    // Payment status constants
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PARTIAL = 'partial';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REFUNDED = 'refunded';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_NO_SHOW => 'No Show',
        ];
    }

    public static function getPaymentStatuses(): array
    {
        return [
            self::PAYMENT_PENDING => 'Pending',
            self::PAYMENT_PARTIAL => 'Partial',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_REFUNDED => 'Refunded',
        ];
    }

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(PropertyCalendarEvent::class, 'booking_id');
    }

    // Scopes
    public function scopeForProperty(Builder $query, int $propertyId): Builder
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED]);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('check_in_date', '>=', Carbon::today());
    }

    public function scopeCurrentlyStaying(Builder $query): Builder
    {
        $today = Carbon::today();
        return $query->where('check_in_date', '<=', $today)
                    ->where('check_out_date', '>', $today)
                    ->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeInDateRange(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('check_in_date', [$startDate, $endDate])
              ->orWhereBetween('check_out_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('check_in_date', '<=', $startDate)
                     ->where('check_out_date', '>=', $endDate);
              });
        });
    }

    // Helper methods
    public function calculateNights(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    public function isActive(): bool
    {
        return !in_array($this->status, [self::STATUS_CANCELLED]);
    }

    public function isUpcoming(): bool
    {
        return $this->check_in_date->isFuture();
    }

    public function isCurrentlyStaying(): bool
    {
        $today = Carbon::today();
        return $this->check_in_date->lte($today) && 
               $this->check_out_date->gt($today) && 
               $this->status === self::STATUS_CONFIRMED;
    }

    public function isPast(): bool
    {
        return $this->check_out_date->isPast();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]) && 
               $this->check_in_date->isFuture();
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => '#ffc107',
            self::STATUS_CONFIRMED => '#28a745',
            self::STATUS_CANCELLED => '#dc3545',
            self::STATUS_COMPLETED => '#17a2b8',
            self::STATUS_NO_SHOW => '#6c757d',
            default => '#007bff',
        };
    }

    public function getStatusLabel(): string
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    public function getPaymentStatusLabel(): string
    {
        return self::getPaymentStatuses()[$this->payment_status] ?? 'Unknown';
    }

    // Static helper methods
    public static function generateBookingNumber(): string
    {
        do {
            $number = 'VR' . date('Y') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('booking_number', $number)->exists());

        return $number;
    }

    public static function checkDateConflict(int $propertyId, Carbon $checkIn, Carbon $checkOut, ?int $excludeBookingId = null): bool
    {
        $query = self::forProperty($propertyId)
            ->active()
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in_date', [$checkIn, $checkOut->copy()->subDay()])
                  ->orWhereBetween('check_out_date', [$checkIn->copy()->addDay(), $checkOut])
                  ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                      $q2->where('check_in_date', '<=', $checkIn)
                         ->where('check_out_date', '>=', $checkOut);
                  });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->exists();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->booking_number) {
                $booking->booking_number = self::generateBookingNumber();
            }
            
            if (!$booking->nights_count) {
                $booking->nights_count = $booking->calculateNights();
            }
        });

        static::created(function ($booking) {
            // Create calendar event for this booking
            PropertyCalendarEvent::create([
                'property_id' => $booking->property_id,
                'booking_id' => $booking->id,
                'title' => "Booking: {$booking->guest_name}",
                'description' => "Guests: {$booking->guests_count}",
                'start_date' => $booking->check_in_date,
                'end_date' => $booking->check_out_date->copy()->subDay(), // End date is exclusive
                'event_type' => 'booking',
                'color' => '#28a745',
            ]);

            // Update property availability
            PropertyAvailability::bulkUpdateAvailability(
                $booking->property_id,
                $booking->getDateRange(),
                ['status' => PropertyAvailability::STATUS_BOOKED]
            );
        });
    }

    private function getDateRange(): array
    {
        $dates = [];
        $currentDate = $this->check_in_date->copy();
        
        while ($currentDate->lt($this->check_out_date)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }
        
        return $dates;
    }
}

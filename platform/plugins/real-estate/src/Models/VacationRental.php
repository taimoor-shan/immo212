<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Media\Facades\RvMedia;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Models\Traits\UniqueId;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class VacationRental extends BaseModel
{
    use HasFactory, UniqueId;

    protected $table = 're_vacation_rentals';

    protected $fillable = [
        'name',
        'description',
        'content', 
        'location',
        'images',
        'price',
        'currency_id',
        'city_id',
        'state_id',
        'country_id',
        'latitude',
        'longitude',
        'check_in_time',
        'check_out_time',
        'minimum_stay',
        'maximum_stay',
        'maximum_guests',
        'cleaning_fee',
        'security_deposit',
        'house_rules',
        'cancellation_policy',
        'status',
        'moderation_status',
        'is_featured',
        'featured_priority',
        'author_id',
        'author_type',
        'expire_date',
        'auto_renew',
        'unique_id',
        'private_notes',
        'reject_reason',
    ];

    protected $casts = [
        'status' => PropertyStatusEnum::class,
        'moderation_status' => ModerationStatusEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
        'content' => SafeContent::class,
        'location' => SafeContent::class,
        'private_notes' => SafeContent::class,
        'house_rules' => SafeContent::class,
        'expire_date' => 'datetime',
        'images' => 'json',
        'price' => 'float',
        'featured_priority' => 'int',
        'minimum_stay' => 'int',
        'maximum_stay' => 'int',
        'maximum_guests' => 'int',
        'cleaning_fee' => 'float',
        'security_deposit' => 'float',
    ];

    protected static function booted(): void
    {
        static::deleting(function (VacationRental $vacationRental): void {
            $vacationRental->categories()->detach();
            $vacationRental->customFields()->delete();
            $vacationRental->reviews()->delete();
            $vacationRental->features()->detach();
            $vacationRental->facilities()->detach();
            // $vacationRental->metadata()->delete();
            $vacationRental->bookings()->delete();
            $vacationRental->availability()->delete();
            $vacationRental->calendarEvents()->delete();
        });
    }

    // Relationships
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function author(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 're_vacation_rental_categories', 'vacation_rental_id', 'category_id');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 're_vacation_rental_features', 'vacation_rental_id', 'feature_id');
    }

    public function facilities(): BelongsToMany
    {
        return $this->morphToMany(Facility::class, 'reference', 're_facilities_distances')->withPivot('distance');
    }

    public function customFields(): MorphMany
    {
        return $this->morphMany(CustomFieldValue::class, 'reference', 'reference_type', 'reference_id')->with('customField.options');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // public function metadata(): MorphMany
    // {
    //     return $this->morphMany(MetaBox::class, 'reference', 'reference_type', 'reference_id');
    // }

    // Vacation rental specific relationships
    public function bookings(): HasMany
    {
        return $this->hasMany(VacationRentalBooking::class, 'vacation_rental_id');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(VacationRentalAvailability::class, 'vacation_rental_id');
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(VacationRentalCalendarEvent::class, 'vacation_rental_id');
    }

    // Location relationships (if location plugin is active)
    public function city(): BelongsTo
    {
        return $this->belongsTo(\Botble\Location\Models\City::class)->withDefault();
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(\Botble\Location\Models\State::class)->withDefault();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(\Botble\Location\Models\Country::class)->withDefault();
    }

    // Attributes
    protected function image(): Attribute
    {
        return Attribute::get(fn () => Arr::first($this->images) ?? null);
    }

    protected function address(): Attribute
    {
        return Attribute::get(fn () => $this->location);
    }

    protected function category(): Attribute
    {
        return Attribute::get(fn () => $this->categories->first() ?: new Category());
    }

    protected function cityName(): Attribute
    {
        return Attribute::get(function () {
            if (! is_plugin_active('location')) {
                return $this->location;
            }

            return ($this->city->name ? $this->city->name . ', ' : null) . $this->state->name;
        });
    }

    protected function statusHtml(): Attribute
    {
        return Attribute::get(fn () => $this->status->toHtml());
    }

    protected function categoryName(): Attribute
    {
        return Attribute::get(fn () => $this->category->name);
    }

    protected function imageThumb(): Attribute
    {
        return Attribute::get(fn () => $this->image ? RvMedia::getImageUrl($this->image, 'thumb', false, RvMedia::getDefaultImage()) : null);
    }

    protected function imageSmall(): Attribute
    {
        return Attribute::get(fn () => $this->image ? RvMedia::getImageUrl($this->image, 'small', false, RvMedia::getDefaultImage()) : null);
    }

    protected function priceHtml(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->price) {
                return __('Contact');
            }

            $price = $this->price_format;
            $price .= ' / ' . __('night');

            return $price;
        });
    }

    protected function priceFormat(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->price) {
                return __('Contact');
            }

            if ($this->price_formatted) {
                return $this->price_formatted;
            }

            $currency = $this->currency;

            if (! $currency || ! $currency->getKey()) {
                $currency = get_application_currency();
            }

            return $this->price_formatted = format_price($this->price, $currency);
        });
    }

    protected function mapIcon(): Attribute
    {
        return Attribute::get(fn () => 'Vacation Rental: ' . $this->price_format);
    }

    protected function customFieldsArray(): Attribute
    {
        return Attribute::get(fn () => CustomFieldValue::getCustomFieldValuesArray($this));
    }

    protected function shortAddress(): Attribute
    {
        return Attribute::get(function () {
            if (! is_plugin_active('location')) {
                return $this->location;
            }

            return implode(', ', array_filter([$this->city->name, $this->state->name]));
        });
    }

    // Helper methods
    public function isVacationRental(): bool
    {
        return true;
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', PropertyStatusEnum::PUBLISHED)
                    ->where('moderation_status', ModerationStatusEnum::APPROVED);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeByAuthor($query, $authorId, $authorType)
    {
        return $query->where('author_id', $authorId)
                    ->where('author_type', $authorType);
    }

    public function scopeByLocation($query, $cityId = null, $stateId = null)
    {
        if ($cityId) {
            $query->where('city_id', $cityId);
        }
        if ($stateId) {
            $query->where('state_id', $stateId);
        }
        return $query;
    }
}

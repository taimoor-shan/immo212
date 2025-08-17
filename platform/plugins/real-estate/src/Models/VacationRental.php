<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Media\Facades\RvMedia;
use Botble\RealEstate\Database\Factories\VacationRentalFactory;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\VacationRentalStatusEnum;
use Botble\RealEstate\Models\Traits\UniqueId;
use Botble\RealEstate\QueryBuilders\VacationRentalBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @method static \Botble\RealEstate\QueryBuilders\VacationRentalBuilder<static> query()
 */
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
        'number_bedroom',
        'number_bathroom',
        'number_floor',
        'square',
        'price',
        'status',
        'moderation_status',
        'is_featured',
        'featured_priority',
        'currency_id',
        'city_id',
        'state_id',
        'country_id',
        'author_id',
        'author_type',
        'expire_date',
        'auto_renew',
        'latitude',
        'longitude',
        'unique_id',
        'private_notes',
        'floor_plans',
        'floor_name',
        'floor_plan_image',
        'floor_plan_document',
        'reject_reason',
        'check_in_time',
        'check_out_time',
        'minimum_stay',
        'maximum_stay',
        'maximum_guests',
        'cleaning_fee',
        'security_deposit',
        'house_rules',
        'cancellation_policy',
    ];

    protected $casts = [
        'status' => VacationRentalStatusEnum::class,
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
        'square' => 'float',
        'number_bedroom' => 'int',
        'number_bathroom' => 'int',
        'number_floor' => 'int',
        'featured_priority' => 'int',
        'floor_plans' => 'array',
        'minimum_stay' => 'int',
        'maximum_stay' => 'int',
        'maximum_guests' => 'int',
        'cleaning_fee' => 'float',
        'security_deposit' => 'float',
        'views' => 'int',
        'is_featured' => 'boolean',
        'auto_renew' => 'boolean',
    ];

    protected static function newFactory(): VacationRentalFactory
    {
        return VacationRentalFactory::new();
    }

    protected static function booted(): void
    {
        static::deleting(function (VacationRental $vacationRental): void {
            $vacationRental->categories()->detach();
            $vacationRental->customFields()->delete();
            $vacationRental->reviews()->delete();
            $vacationRental->features()->detach();
            $vacationRental->facilities()->detach();
            $vacationRental->metadata()->delete();
            $vacationRental->availability()->delete();
            $vacationRental->availabilityRules()->delete();
            $vacationRental->bookings()->delete();
            $vacationRental->calendarEvents()->delete();
        });
    }

    // Relationships
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 're_vacation_rental_features', 'vacation_rental_id', 'feature_id');
    }

    public function facilities(): BelongsToMany
    {
        return $this->morphToMany(Facility::class, 'reference', 're_vacation_rental_facilities_distances')->withPivot('distance');
    }

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

    public function customFields(): MorphMany
    {
        return $this->morphMany(CustomFieldValue::class, 'reference', 'reference_type', 'reference_id')->with('customField.options');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(VacationRentalAvailability::class);
    }

    public function availabilityRules(): HasMany
    {
        return $this->hasMany(VacationRentalAvailabilityRule::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(VacationRentalBooking::class);
    }

    public function vacationRentalBookings(): HasMany
    {
        return $this->hasMany(VacationRentalBooking::class);
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(VacationRentalCalendarEvent::class);
    }

    public function newEloquentBuilder($query): VacationRentalBuilder
    {
        return new VacationRentalBuilder($query);
    }

    // Attributes
    protected function image(): Attribute
    {
        return Attribute::get(fn () => Arr::first($this->images) ?? null);
    }

    protected function squareText(): Attribute
    {
        return Attribute::get(function () {
            $square = $this->square;
            $unit = setting('real_estate_square_unit', 'm²');
            return apply_filters('real_estate_vacation_rental_square_text', sprintf('%s %s', number_format($square), __($unit)), $square);
        });
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
            return $this->price_format . ' / ' . __('night');
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
        return Attribute::get(fn () => __('Vacation Rental') . ': ' . $this->price_format);
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

            $addressParts = [];

            // Safely get city name
            if ($this->city && $this->city->name) {
                $addressParts[] = $this->city->name;
            }

            // Safely get state name
            if ($this->state && $this->state->name) {
                $addressParts[] = $this->state->name;
            }

            // If no city/state found, fallback to location field
            if (empty($addressParts)) {
                return $this->location;
            }

            return implode(', ', $addressParts);
        });
    }

    protected function isPendingModeration(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->exists) {
                return false;
            }
            return ! in_array($this->moderation_status, [ModerationStatusEnum::APPROVED, ModerationStatusEnum::REJECTED]);
        });
    }

    // Floor plan methods (copied from Property model)
    protected function formattedFloorPlans(): Attribute
    {
        return Attribute::get(function () {
            $floorPlan = $this->floor_plans;

            if (! is_array($floorPlan)) {
                $floorPlan = json_decode($floorPlan, true);
            }

            return collect($floorPlan)
                ->filter(fn ($floorPlan) => is_array($floorPlan))
                ->map(function ($floorPlan) {
                    $floorPlan = collect($floorPlan)->pluck('value', 'key')->toArray();
                    $bedrooms = (int) Arr::get($floorPlan, 'bedrooms', 0);
                    $bathrooms = (int) Arr::get($floorPlan, 'bathrooms', 0);

                    return [
                        'name' => Arr::get($floorPlan, 'name'),
                        'description' => Arr::get($floorPlan, 'description'),
                        'image' => Arr::get($floorPlan, 'image'),
                        'bedrooms' => $bedrooms === 1 ? __('1 bedroom') : __(':count bedrooms', ['count' => $bedrooms]),
                        'bathrooms' => $bathrooms === 1 ? __('1 bathroom') : __(':count bathrooms', ['count' => $bathrooms]),
                    ];
                });
        });
    }

    public function shouldUseSingleFloorPlan(): bool
    {
        return $this->number_floor === 1;
    }

    public function shouldUseMultipleFloorPlans(): bool
    {
        return $this->number_floor > 1;
    }

    public function getConditionalFloorPlanData(): array
    {
        if ($this->shouldUseSingleFloorPlan()) {
            return [
                'type' => 'single',
                'floor_name' => $this->floor_name,
                'floor_plan_image' => $this->floor_plan_image,
                'floor_plan_document' => $this->floor_plan_document,
            ];
        }

        if ($this->shouldUseMultipleFloorPlans()) {
            return [
                'type' => 'multiple',
                'floor_plans' => $this->formatted_floor_plans,
            ];
        }

        return ['type' => 'none'];
    }
}

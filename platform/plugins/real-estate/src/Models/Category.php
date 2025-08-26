<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Contracts\HasTreeCategory as HasTreeCategoryContract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\HasTreeCategory;
use Botble\RealEstate\Enums\CategoryTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\HtmlString;

class Category extends BaseModel implements HasTreeCategoryContract
{
    use HasTreeCategory;

    protected $table = 're_categories';

    protected $fillable = [
        'name',
        'description',
        'status',
        'order',
        'is_default',
        'parent_id',
        'category_types',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
        'category_types' => 'json',
    ];

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 're_property_categories')->with('slugable');
    }

    public function vacationRentals(): BelongsToMany
    {
        return $this->belongsToMany(VacationRental::class, 're_vacation_rental_categories')->with('slugable');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 're_project_categories')->with('slugable');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id')->withDefault();
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Scope to filter categories by type
     */
    public function scopeForType(Builder $query, string $type): Builder
    {
        return $query->whereJsonContains('category_types', $type)
            ->orWhereNull('category_types'); // Include categories with no type restriction for backward compatibility
    }

    /**
     * Scope to get categories suitable for vacation rentals
     */
    public function scopeForVacationRentals(Builder $query): Builder
    {
        return $query->forType(CategoryTypeEnum::VACATION_RENTAL);
    }

    /**
     * Scope to get categories suitable for properties
     */
    public function scopeForProperties(Builder $query): Builder
    {
        return $query->forType(CategoryTypeEnum::PROPERTY);
    }

    /**
     * Scope to get categories suitable for projects
     */
    public function scopeForProjects(Builder $query): Builder
    {
        return $query->forType(CategoryTypeEnum::PROJECT);
    }

    /**
     * Check if category is suitable for a specific type
     */
    public function isForType(string $type): bool
    {
        if (empty($this->category_types)) {
            return true; // Backward compatibility - categories with no types work for all
        }
        
        return in_array($type, $this->category_types);
    }

    /**
     * Check if category is suitable for vacation rentals
     */
    public function isForVacationRentals(): bool
    {
        return $this->isForType(CategoryTypeEnum::VACATION_RENTAL);
    }

    /**
     * Check if category is suitable for properties
     */
    public function isForProperties(): bool
    {
        return $this->isForType(CategoryTypeEnum::PROPERTY);
    }

    /**
     * Check if category is suitable for projects
     */
    public function isForProjects(): bool
    {
        return $this->isForType(CategoryTypeEnum::PROJECT);
    }

    /**
     * Get the formatted category types for display
     */
    protected function categoryTypesHtml(): Attribute
    {
        return Attribute::get(function (): HtmlString {
            if (empty($this->category_types)) {
                return new HtmlString(BaseHelper::renderBadge(__('All Types'), 'secondary'));
            }

            $html = '';
            foreach ($this->category_types as $type) {
                $enum = CategoryTypeEnum::from($type);
                $html .= $enum->toHtml() . ' ';
            }

            return new HtmlString(trim($html));
        });
    }

    protected function badgeWithCount(): Attribute
    {
        return Attribute::get(function (): HtmlString {
            $html = '';

            if ($this->is_default) {
                $html .= sprintf(
                    '<span class="text-success" data-bs-toggle="tooltip" title="%s">%s</span>',
                    trans('plugins/real-estate::category.is_default'),
                    BaseHelper::renderIcon('ti ti-check', 'sm')
                );
            }

            $html .= BaseHelper::renderBadge(
                $this->projects_count,
                'info',
                [
                    'data-bs-toggle' => 'tooltip',
                    'title' => trans('plugins/real-estate::category.total_projects', ['total' => $this->projects_count]),
                ]
            );

            $html .= BaseHelper::renderBadge(
                $this->properties_count,
                'info',
                [
                    'data-bs-toggle' => 'tooltip',
                    'title' => trans('plugins/real-estate::category.total_properties', ['total' => $this->properties_count]),
                ]
            );

            // Add vacation rentals count if relationship is loaded
            if ($this->relationLoaded('vacationRentals')) {
                $html .= BaseHelper::renderBadge(
                    $this->vacation_rentals_count ?? 0,
                    'warning',
                    [
                        'data-bs-toggle' => 'tooltip',
                        'title' => trans('Vacation Rentals: :total', ['total' => $this->vacation_rentals_count ?? 0]),
                    ]
                );
            }

            return new HtmlString($html);
        });
    }

    protected static function booted(): void
    {
        self::deleting(function (Category $category): void {
            foreach ($category->children()->get() as $child) {
                $child->parent_id = $category->parent_id;
                $child->save();
            }

            $category->properties()->detach();
            $category->vacationRentals()->detach();
            $category->projects()->detach();
        });
    }
}

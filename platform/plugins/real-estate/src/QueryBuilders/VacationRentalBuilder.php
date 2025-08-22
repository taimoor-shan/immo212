<?php

namespace Botble\RealEstate\QueryBuilders;

use Botble\Base\Models\BaseQueryBuilder;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\VacationRentalStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Illuminate\Database\Eloquent\Builder;

class VacationRentalBuilder extends BaseQueryBuilder
{
    public function active(): static
    {
        return $this->wherePublished()->approved()->notExpired();
    }

    public function notExpired(): static
    {
        return $this->where(function (Builder $query): void {
            $query
                ->whereNull('expire_date')
                ->orWhere('expire_date', '>=', now());
        });
    }

    public function approved(): static
    {
        return $this->where('moderation_status', ModerationStatusEnum::APPROVED);
    }

    public function featured(): static
    {
        return $this->where('is_featured', true);
    }

    public function inCity(int $cityId): static
    {
        return $this->where('city_id', $cityId);
    }

    public function inState(int $stateId): static
    {
        return $this->where('state_id', $stateId);
    }

    public function inCountry(int $countryId): static
    {
        return $this->where('country_id', $countryId);
    }

    public function byAuthor(int $authorId, ?string $authorType = null): static
    {
        $query = $this->where('author_id', $authorId);

        if ($authorType) {
            $query->where('author_type', $authorType);
        }

        return $query;
    }

    public function withMinimumStay(int $nights): static
    {
        return $this->where(function (Builder $query) use ($nights): void {
            $query
                ->whereNull('minimum_stay')
                ->orWhere('minimum_stay', '<=', $nights);
        });
    }

    public function withMaximumGuests(int $guests): static
    {
        return $this->where(function (Builder $query) use ($guests): void {
            $query
                ->whereNull('maximum_guests')
                ->orWhere('maximum_guests', '>=', $guests);
        });
    }

    public function priceRange(?float $minPrice = null, ?float $maxPrice = null): static
    {
        if ($minPrice !== null) {
            $this->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $this->where('price', '<=', $maxPrice);
        }

        return $this;
    }

    public function withBedrooms(int $bedrooms): static
    {
        return $this->where('number_bedroom', '>=', $bedrooms);
    }

    public function withBathrooms(int $bathrooms): static
    {
        return $this->where('number_bathroom', '>=', $bathrooms);
    }

    public function withSquareRange(?float $minSquare = null, ?float $maxSquare = null): static
    {
        if ($minSquare !== null) {
            $this->where('square', '>=', $minSquare);
        }

        if ($maxSquare !== null) {
            $this->where('square', '<=', $maxSquare);
        }

        return $this;
    }

    public function withFeatures(array $featureIds): static
    {
        return $this->whereHas('features', function (Builder $query) use ($featureIds): void {
            $query->whereIn('re_features.id', $featureIds);
        });
    }

    public function withCategories(array $categoryIds): static
    {
        return $this->whereHas('categories', function (Builder $query) use ($categoryIds): void {
            $query->whereIn('re_categories.id', $categoryIds);
        });
    }

    public function withFacilities(array $facilityIds): static
    {
        return $this->whereHas('facilities', function (Builder $query) use ($facilityIds): void {
            $query->whereIn('re_facilities.id', $facilityIds);
        });
    }

    public function searchByKeyword(string $keyword): static
    {
        return $this->where(function (Builder $query) use ($keyword): void {
            $query
                ->where('name', 'LIKE', "%{$keyword}%")
                ->orWhere('description', 'LIKE', "%{$keyword}%")
                ->orWhere('location', 'LIKE', "%{$keyword}%")
                ->orWhere('content', 'LIKE', "%{$keyword}%");
        });
    }

    public function orderByFeatured(): static
    {
        return $this->orderByDesc('is_featured')
            ->orderByDesc('featured_priority');
    }

    public function orderByNewest(): static
    {
        return $this->orderByDesc('created_at');
    }

    public function orderByPrice(string $direction = 'asc'): static
    {
        return $this->orderBy('price', $direction);
    }

    public function orderByViews(): static
    {
        return $this->orderByDesc('views');
    }

    public function available(): static
    {
        return $this->active()
            ->approved()
            ->notExpired();
    }
}

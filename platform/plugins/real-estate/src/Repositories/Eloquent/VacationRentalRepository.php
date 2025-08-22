<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Language\Facades\Language;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\VacationRental;
use Botble\RealEstate\Repositories\Interfaces\VacationRentalInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Contracts\Database\Eloquent\Builder as BaseQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VacationRentalRepository extends RepositoriesAbstract implements VacationRentalInterface
{
    public function getRelatedVacationRentals(int $vacationRentalId, int $limit = 4, array $with = [], array $extra = []): Collection|LengthAwarePaginator
    {
        $limit = $limit > 1 ? $limit : 4;
        $currentVacationRental = $this->findById($vacationRentalId, ['categories']);

        $this->model = $this->originalModel;

        // @phpstan-ignore-next-line
        $this->model = $this->model
            ->where('id', '<>', $vacationRentalId)
            ->wherePublished()
            ->approved()
            ->notExpired();

        if ($currentVacationRental && $currentVacationRental->categories->count()) {
            $categoryIds = $currentVacationRental->categories->pluck('id')->toArray();

            $this->model
                ->whereHas('categories', function ($query) use ($categoryIds): void {
                    $query->whereIn('category_id', $categoryIds);
                });
        }

        $params = array_merge([
            'condition' => [],
            'order_by' => [
                'created_at' => 'DESC',
            ],
            'take' => $limit,
            'with' => $with,
        ], $extra);

        return $this->advancedGet($params);
    }

    public function getVacationRentals(array $filters = [], array $params = []): Collection|LengthAwarePaginator
    {
        $filters = array_merge([
            'keyword' => null,
            'bedroom' => null,
            'bathroom' => null,
            'floor' => null,
            'min_square' => null,
            'max_square' => null,
            'min_price' => null,
            'max_price' => null,
            'category_id' => null,
            'author_id' => null,
            'city_id' => null,
            'city' => null,
            'state' => null,
            'state_id' => null,
            'location' => null,
            'sort_by' => null,
            'features' => null,
            'minimum_stay' => null,
            'maximum_guests' => null,
            'locations' => null,
            'category_ids' => null,
            'check_in_date' => null,
            'check_out_date' => null,
            'maximum_stay' => null,
        ], $filters);

        $orderBy = match ($filters['sort_by']) {
            'date_asc' => [
                'created_at' => 'ASC',
            ],
            'price_asc' => [
                'price' => 'ASC',
            ],
            'price_desc' => [
                'price' => 'DESC',
            ],
            'name_asc' => [
                'name' => 'ASC',
            ],
            'name_desc' => [
                'name' => 'DESC',
            ],
            default => [
                'created_at' => 'DESC',
            ],
        };

        $params = array_merge([
            'condition' => [],
            'order_by' => [
                'created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => 10,
                'current_paged' => 1,
            ],
            'select' => [
                '*',
            ],
            'with' => [],
        ], $params);

        // Initialize the model with active vacation rentals
        $this->model = $this->originalModel
            ->where('status', '!=', BaseStatusEnum::DRAFT)
            ->where('moderation_status', ModerationStatusEnum::APPROVED)
            ->notExpired()
            ;

        // Always sort by featured status first, then by featured_priority, then by the regular ordering
        $this->model = $this->model
            // First sort by featured status (featured vacation rentals first)
            ->orderByDesc('is_featured')
            // Then sort featured vacation rentals by their priority (higher values first)
            ->orderByDesc('featured_priority');

        // Then apply the regular ordering for vacation rentals with same featured status and priority
        foreach ($orderBy as $column => $direction) {
            $this->model = $this->model->orderBy($column, $direction);
        }


        // @phpstan-ignore-next-line

        if ($filters['keyword'] !== null) {
            $keyword = $filters['keyword'];

            if (is_plugin_active('language') && is_plugin_active('language-advanced') && Language::getCurrentLocale() != Language::getDefaultLocale()) {
                $this->model = $this->model
                    ->whereHas('translations', function (BaseQueryBuilder $query) use ($keyword): void {
                        $query
                            ->addSearch('name', $keyword, false, false)
                            ->addSearch('location', $keyword, false)
                            ->addSearch('description', $keyword, false)
                            ->addSearch('unique_id', $keyword, false);
                    });
            } else {
                $this->model = $this->model
                    ->where(function (BaseQueryBuilder $query) use ($keyword) {
                        return $query
                            ->addSearch('name', $keyword, false, false)
                            ->addSearch('location', $keyword, false)
                            ->addSearch('description', $keyword, false)
                            ->addSearch('unique_id', $keyword, false);
                    });
            }
        }

        if ($filters['bedroom']) {
            if ($filters['bedroom'] < 5) {
                $this->model = $this->model->where('number_bedroom', $filters['bedroom']);
            } else {
                $this->model = $this->model->where('number_bedroom', '>=', $filters['bedroom']);
            }
        }

        if ($filters['bathroom']) {
            if ($filters['bathroom'] < 5) {
                $this->model = $this->model->where('number_bathroom', $filters['bathroom']);
            } else {
                $this->model = $this->model->where('number_bathroom', '>=', $filters['bathroom']);
            }
        }

        if ($filters['floor']) {
            if ($filters['floor'] < 5) {
                $this->model = $this->model->where('number_floor', $filters['floor']);
            } else {
                $this->model = $this->model->where('number_floor', '>=', $filters['floor']);
            }
        }

        if ($filters['min_square'] !== null || $filters['max_square'] !== null) {
            $this->model = $this->model
                ->where(function (Builder $query) use ($filters) {
                    $minSquare = Arr::get($filters, 'min_square');
                    $maxSquare = Arr::get($filters, 'max_square');

                    if ($minSquare !== null) {
                        $query = $query->where('square', '>=', $minSquare);
                    }

                    if ($maxSquare !== null) {
                        $query = $query->where('square', '<=', $maxSquare);
                    }

                    return $query;
                });
        }

        if ($filters['min_price'] !== null || $filters['max_price'] !== null) {
            $this->model = $this->model
                ->where(function (Builder $query) use ($filters) {
                    $minPrice = Arr::get($filters, 'min_price');
                    $maxPrice = Arr::get($filters, 'max_price');

                    if ($minPrice !== null) {
                        $query = $query->where('price', '>=', $minPrice);
                    }

                    if ($maxPrice !== null) {
                        $query = $query->where('price', '<=', $maxPrice);
                    }

                    return $query;
                });
        }

        if ($filters['city'] !== null) {
            $this->model = $this->model->whereHas('city', function ($query) use ($filters): void {
                $query->where('slug', $filters['city']);
            });
        }

        if ($filters['state'] !== null) {
            $this->model = $this->model->whereHas('state', function ($query) use ($filters): void {
                $query->where('slug', $filters['state']);
            });
        }

        if ($filters['city_id'] !== null) {
            $this->model = $this->model->where('city_id', $filters['city_id']);
        }

        if ($filters['state_id'] !== null) {
            $this->model = $this->model->where('state_id', $filters['state_id']);
        }

        if ($filters['location'] !== null) {
            $this->model = $this->model->where('location', 'LIKE', '%' . $filters['location'] . '%');
        }

        if ($filters['category_id'] !== null) {
            $this->model = $this->model->whereHas('categories', function ($query) use ($filters): void {
                $query->where('category_id', $filters['category_id']);
            });
        }

        if ($filters['category_ids'] !== null) {
            $categoryIds = array_filter((array) $filters['category_ids']);

            if ($categoryIds) {
                $this->model = $this->model->whereHas('categories', function ($query) use ($categoryIds): void {
                    $query->whereIn('category_id', $categoryIds);
                });
            }
        }

        if ($filters['locations'] !== null) {
            $locations = array_filter((array) $filters['locations']);

            if ($locations) {
                $this->model = $this->model->where(function ($query) use ($locations): void {
                    foreach ($locations as $location) {
                        $query->orWhere('location', 'LIKE', '%' . $location . '%');
                    }
                });
            }
        }

        if ($filters['author_id'] !== null) {
            $this->model = $this->model
                ->where('author_id', $filters['author_id'])
                ->where('author_type', Account::class);
        }

        // Vacation rental specific filters
        if ($filters['minimum_stay'] !== null) {
            $this->model = $this->model->where('minimum_stay', '<=', $filters['minimum_stay']);
        }

        if ($filters['maximum_stay'] !== null) {
            $this->model = $this->model->where(function (Builder $query) use ($filters) {
                $query->whereNull('maximum_stay')
                      ->orWhere('maximum_stay', '>=', $filters['maximum_stay']);
            });
        }

        if ($filters['maximum_guests'] !== null) {
            $this->model = $this->model->where('maximum_guests', '>=', $filters['maximum_guests']);
        }

        // Date availability filtering with comprehensive validation
        // This filtering replicates the logic from the frontend booking calendar to ensure
        // that only truly available vacation rentals are shown in search results.
        // It validates:
        // 1. Minimum stay requirements (property-level and rule-based overrides)
        // 2. Maximum stay requirements (property-level and rule-based overrides)
        // 3. No conflicting bookings (pending or confirmed)
        // 4. No blocked, booked, or maintenance dates in the selected range
        // 5. Availability rules that might block certain date ranges or days of week
        if ($filters['check_in_date'] !== null && $filters['check_out_date'] !== null) {
            $checkInDate = Carbon::parse($filters['check_in_date']);
            $checkOutDate = Carbon::parse($filters['check_out_date']);
            $nights = $checkInDate->diffInDays($checkOutDate);

            // Filter vacation rentals based on comprehensive availability check
            $this->model = $this->model->where(function (Builder $query) use ($checkInDate, $checkOutDate, $nights) {
                $query->whereExists(function ($subQuery) use ($checkInDate, $checkOutDate, $nights) {
                    $subQuery->selectRaw('1')
                        ->from('re_vacation_rentals as vr_check')
                        ->whereColumn('vr_check.id', 're_vacation_rentals.id')
                        ->where(function ($availabilityQuery) use ($checkInDate, $checkOutDate, $nights) {
                            // Check base minimum stay requirement (property level)
                            $availabilityQuery->where(function ($minStayQuery) use ($nights) {
                                $minStayQuery->whereNull('minimum_stay')
                                    ->orWhere('minimum_stay', '<=', $nights);
                            })
                            // Check base maximum stay requirement (property level)
                            ->where(function ($maxStayQuery) use ($nights) {
                                $maxStayQuery->whereNull('maximum_stay')
                                    ->orWhere('maximum_stay', '>=', $nights);
                            })
                            // Check availability rules don't override stay requirements
                            ->whereNotExists(function ($rulesQuery) use ($checkInDate, $nights) {
                                $rulesQuery->select(DB::raw(1))
                                    ->from('re_vacation_rental_availability_rules')
                                    ->whereColumn('vacation_rental_id', 'vr_check.id')
                                    ->where('is_active', true)
                                    ->where(function ($ruleConditions) use ($checkInDate, $nights) {
                                        $ruleConditions
                                            // Rules with minimum stay override that's higher than our nights
                                            ->where(function ($minStayRuleQuery) use ($nights) {
                                                $minStayRuleQuery->whereNotNull('minimum_stay_override')
                                                    ->where('minimum_stay_override', '>', $nights);
                                            })
                                            // OR rules with maximum stay override that's lower than our nights
                                            ->orWhere(function ($maxStayRuleQuery) use ($nights) {
                                                $maxStayRuleQuery->whereNotNull('maximum_stay_override')
                                                    ->where('maximum_stay_override', '<', $nights);
                                            });
                                    })
                                    ->where(function ($dateApplicability) use ($checkInDate) {
                                        $dateApplicability
                                            // Date range rules applicable to check-in date
                                            ->where(function ($dateRangeQuery) use ($checkInDate) {
                                                $dateRangeQuery->whereNotNull('start_date')
                                                    ->whereNotNull('end_date')
                                                    ->where('start_date', '<=', $checkInDate->format('Y-m-d'))
                                                    ->where('end_date', '>=', $checkInDate->format('Y-m-d'));
                                            })
                                            // OR weekly rules applicable to check-in day of week
                                            ->orWhere(function ($weeklyQuery) use ($checkInDate) {
                                                $weeklyQuery->where('rule_type', 'weekly')
                                                    ->whereJsonContains('days_of_week', $checkInDate->dayOfWeek);
                                            });
                                    });
                            })
                            // Check no conflicting bookings
                            ->whereNotExists(function ($bookingQuery) use ($checkInDate, $checkOutDate) {
                                $bookingQuery->select(DB::raw(1))
                                    ->from('re_vacation_rental_bookings')
                                    ->whereColumn('vacation_rental_id', 'vr_check.id')
                                    ->whereIn('status', ['pending', 'confirmed'])
                                    ->where(function ($dateQuery) use ($checkInDate, $checkOutDate) {
                                        $dateQuery->whereBetween('check_in_date', [$checkInDate, $checkOutDate->copy()->subDay()])
                                            ->orWhereBetween('check_out_date', [$checkInDate->copy()->addDay(), $checkOutDate])
                                            ->orWhere(function ($overlapQuery) use ($checkInDate, $checkOutDate) {
                                                $overlapQuery->where('check_in_date', '<=', $checkInDate)
                                                    ->where('check_out_date', '>=', $checkOutDate);
                                            });
                                    });
                            })
                            // Check no blocked/maintenance dates in range
                            ->whereNotExists(function ($availabilityQuery) use ($checkInDate, $checkOutDate) {
                                $availabilityQuery->select(DB::raw(1))
                                    ->from('re_vacation_rental_availability')
                                    ->whereColumn('vacation_rental_id', 'vr_check.id')
                                    ->whereBetween('date', [$checkInDate, $checkOutDate->copy()->subDay()])
                                    ->whereIn('status', ['blocked', 'booked', 'maintenance']);
                            });
                        });
                });
            });
        }

        if ($filters['features'] !== null) {
            $features = array_filter((array) $filters['features']);

            if ($features) {
                $vacationRentalIds = $this
                    ->getModel()
                    ->toBase()
                    ->select('re_vacation_rentals.id')
                    ->join('re_vacation_rental_features', 're_vacation_rentals.id', '=', 're_vacation_rental_features.vacation_rental_id')
                    ->whereIn('re_vacation_rental_features.feature_id', $features)
                    ->groupBy('re_vacation_rentals.id')
                    ->havingRaw('COUNT(DISTINCT re_vacation_rental_features.feature_id) = ' . count($features))
                    ->pluck('re_vacation_rentals.id')
                    ->all();

                $this->model = $this->model->whereIn('id', $vacationRentalIds);
            }
        }

        $this->model = apply_filters('vacation_rentals_filter_query', $this->model, $filters, $params);

        //  dd($filters);

        return $this->advancedGet($params);
    }

    public function getVacationRental(int $vacationRentalId, array $with = [], array $extra = []): ?VacationRental
    {
        $params = array_merge([
            'condition' => [
                'id' => $vacationRentalId,
                'moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            'with' => $with,
            'take' => 1,
        ], $extra);

        // @phpstan-ignore-next-line
        $this->model = $this->originalModel
            ->wherePublished()
            ->notExpired();

        return $this->advancedGet($params);
    }

    public function getVacationRentalsByConditions(array $condition, int $limit = 4, array $with = []): Collection|LengthAwarePaginator
    {
        $limit = $limit > 1 ? $limit : 4;

        // @phpstan-ignore-next-line
        $this->model = $this->originalModel
            ->wherePublished()
            ->approved()
            ->notExpired();

        $params = [
            'condition' => $condition,
            'with' => $with,
            'take' => $limit,
            'order_by' => ['created_at' => 'DESC'],
        ];

        return $this->advancedGet($params);
    }
}

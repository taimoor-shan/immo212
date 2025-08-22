<?php

namespace Botble\RealEstate\Repositories\Interfaces;

use Botble\RealEstate\Models\VacationRental;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface VacationRentalInterface extends RepositoryInterface
{
    public function getVacationRentals(array $filters = [], array $params = []): Collection|LengthAwarePaginator;

    public function getRelatedVacationRentals(int $vacationRentalId, int $limit = 4, array $with = [], array $extra = []): Collection|LengthAwarePaginator;

    public function getVacationRental(int $vacationRentalId, array $with = [], array $extra = []): ?VacationRental;

    public function getVacationRentalsByConditions(array $condition, int $limit = 4, array $with = []): Collection|LengthAwarePaginator;
}

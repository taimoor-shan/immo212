<?php

namespace Botble\RealEstate\QueryBuilders;

use Botble\Base\Models\BaseQueryBuilder;
use Botble\RealEstate\Facades\RealEstateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class ProjectBuilder extends BaseQueryBuilder
{
    public function notExpired(): static
    {
        if (Schema::hasColumn('re_projects', 'expire_date')) {
            $this->where(function (Builder $query): void {
                $query
                    ->where('expire_date', '>=', Carbon::now()->toDateTimeString())
                    ->orWhere('never_expired', true);
            });
        }

        return $this;
    }

    public function expired(): static
    {
        if (Schema::hasColumn('re_projects', 'expire_date')) {
            $this->where(function (Builder $query): void {
                $query
                    ->where('expire_date', '<', Carbon::now()->toDateTimeString())
                    ->where('never_expired', false);
            });
        }

        return $this;
    }

    public function active(): static
    {
        $this
            ->where(RealEstateHelper::getProjectDisplayQueryConditions())
            ->notExpired();

        return $this;
    }
}

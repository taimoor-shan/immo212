<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\VacationRental;
use Illuminate\Http\Request;

class StoreVacationRentalCategoryService
{
    public function execute(Request $request, VacationRental $vacationRental): void
    {
        $categories = $request->input('categories', []);
        if (is_array($categories)) {
            if ($categories) {
                $vacationRental->categories()->sync($categories);
            } else {
                $vacationRental->categories()->detach();
            }
        }
    }
}

<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Http\Requests\VacationRentalRequest as BaseRequest;

class AccountVacationRentalRequest extends BaseRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        unset($rules['moderation_status']);

        return [
            ...$rules,
            'content' => ['required', 'string', 'max:300000'],
        ];
    }
}

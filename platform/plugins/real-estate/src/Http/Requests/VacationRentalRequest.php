<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class VacationRentalRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'images' => ['nullable'],
            'location' => ['nullable', 'string', 'max:300'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency_id' => ['nullable', 'exists:re_currencies,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'latitude' => ['nullable', 'max:20', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['nullable', 'max:20', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            
            // Property-like fields
            'number_bedroom' => ['numeric', 'min:0', 'max:100000', 'nullable'],
            'number_bathroom' => ['numeric', 'min:0', 'max:100000', 'nullable'],
            'number_floor' => ['numeric', 'min:0', 'max:100000', 'nullable'],
            'square' => ['numeric', 'min:0', 'nullable'],
            
            // Vacation rental specific fields
            'check_in_time' => ['nullable'],
            'check_out_time' => ['nullable'],
            'minimum_stay' => ['nullable', 'integer', 'min:1', 'max:365'],
            'maximum_stay' => ['nullable', 'integer', 'min:1', 'max:365', 'gte:minimum_stay'],
            'maximum_guests' => ['nullable', 'integer', 'min:1', 'max:50'],
            'cleaning_fee' => ['nullable', 'numeric', 'min:0'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'house_rules' => ['nullable', 'string'],
            'cancellation_policy' => ['nullable', 'string'],
            
            // Categories and features
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:re_categories,id'],
            'features' => ['nullable', 'array'],
            'features.*' => ['exists:re_features,id'],
            
            // Status and moderation
            'status' => Rule::in(BaseStatusEnum::values()),
            'moderation_status' => Rule::in(ModerationStatusEnum::values()),
            'is_featured' => ['nullable', 'boolean'],
            'featured_priority' => ['nullable', 'integer', 'min:0', 'max:999'],
            'unique_id' => 'nullable|string|max:120|unique:re_vacation_rentals,unique_id,' . $this->route('vacation_rental'),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('validation.required', ['attribute' => trans('plugins/real-estate::vacation-rental.name')]),
            'name.max' => trans('validation.max.string', ['attribute' => trans('plugins/real-estate::vacation-rental.name'), 'max' => 120]),
            'description.max' => trans('validation.max.string', ['attribute' => trans('plugins/real-estate::vacation-rental.description'), 'max' => 1000]),
            'location.max' => trans('validation.max.string', ['attribute' => trans('plugins/real-estate::vacation-rental.location'), 'max' => 300]),
            'price.numeric' => trans('validation.numeric', ['attribute' => trans('plugins/real-estate::vacation-rental.price')]),
            'price.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::vacation-rental.price'), 'min' => 0]),
            'check_in_time.date_format' => trans('validation.date_format', ['attribute' => trans('plugins/real-estate::vacation-rental.check_in_time'), 'format' => 'H:i']),
            'check_out_time.date_format' => trans('validation.date_format', ['attribute' => trans('plugins/real-estate::vacation-rental.check_out_time'), 'format' => 'H:i']),
            'minimum_stay.integer' => trans('validation.integer', ['attribute' => trans('plugins/real-estate::vacation-rental.minimum_stay')]),
            'minimum_stay.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::vacation-rental.minimum_stay'), 'min' => 1]),
            'minimum_stay.max' => trans('validation.max.numeric', ['attribute' => trans('plugins/real-estate::vacation-rental.minimum_stay'), 'max' => 365]),
            'maximum_stay.gte' => trans('validation.gte.numeric', ['attribute' => trans('plugins/real-estate::vacation-rental.maximum_stay'), 'value' => trans('plugins/real-estate::vacation-rental.minimum_stay')]),
            'maximum_guests.integer' => trans('validation.integer', ['attribute' => trans('plugins/real-estate::vacation-rental.maximum_guests')]),
            'maximum_guests.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::vacation-rental.maximum_guests'), 'min' => 1]),
            'maximum_guests.max' => trans('validation.max.numeric', ['attribute' => trans('plugins/real-estate::vacation-rental.maximum_guests'), 'max' => 50]),
            'cleaning_fee.numeric' => trans('validation.numeric', ['attribute' => trans('plugins/real-estate::vacation-rental.cleaning_fee')]),
            'cleaning_fee.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::vacation-rental.cleaning_fee'), 'min' => 0]),
            'security_deposit.numeric' => trans('validation.numeric', ['attribute' => trans('plugins/real-estate::vacation-rental.security_deposit')]),
            'security_deposit.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::vacation-rental.security_deposit'), 'min' => 0]),
            'categories.*.exists' => trans('validation.exists', ['attribute' => trans('plugins/real-estate::vacation-rental.categories')]),
            'features.*.exists' => trans('validation.exists', ['attribute' => trans('plugins/real-estate::vacation-rental.features')]),
            'status.in' => trans('validation.in', ['attribute' => trans('core/base::tables.status')]),
            'moderation_status.in' => trans('validation.in', ['attribute' => trans('plugins/real-estate::vacation-rental.moderation_status')]),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('plugins/real-estate::vacation-rental.name'),
            'description' => trans('plugins/real-estate::vacation-rental.description'),
            'content' => trans('plugins/real-estate::vacation-rental.content'),
            'location' => trans('plugins/real-estate::vacation-rental.location'),
            'price' => trans('plugins/real-estate::vacation-rental.price'),
            'currency_id' => trans('plugins/real-estate::vacation-rental.currency'),
            'check_in_time' => trans('plugins/real-estate::vacation-rental.check_in_time'),
            'check_out_time' => trans('plugins/real-estate::vacation-rental.check_out_time'),
            'minimum_stay' => trans('plugins/real-estate::vacation-rental.minimum_stay'),
            'maximum_stay' => trans('plugins/real-estate::vacation-rental.maximum_stay'),
            'maximum_guests' => trans('plugins/real-estate::vacation-rental.maximum_guests'),
            'cleaning_fee' => trans('plugins/real-estate::vacation-rental.cleaning_fee'),
            'security_deposit' => trans('plugins/real-estate::vacation-rental.security_deposit'),
            'house_rules' => trans('plugins/real-estate::vacation-rental.house_rules'),
            'cancellation_policy' => trans('plugins/real-estate::vacation-rental.cancellation_policy'),
            'categories' => trans('plugins/real-estate::vacation-rental.categories'),
            'features' => trans('plugins/real-estate::vacation-rental.features'),
            'status' => trans('core/base::tables.status'),
            'moderation_status' => trans('plugins/real-estate::vacation-rental.moderation_status'),
            'is_featured' => trans('plugins/real-estate::vacation-rental.is_featured'),
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}

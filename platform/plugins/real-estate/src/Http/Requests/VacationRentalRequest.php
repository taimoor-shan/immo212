<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Enums\VacationRentalStatusEnum;
use Botble\Support\Http\Requests\Request;

class VacationRentalRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:300',
            'description' => 'nullable|string|max:400',
            'content' => 'nullable|string',
            'location' => 'nullable|string|max:300',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string',
            'number_bedroom' => 'nullable|integer|min:0|max:100',
            'number_bathroom' => 'nullable|integer|min:0|max:100',
            'number_floor' => 'nullable|integer|min:0|max:100',
            'square' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|integer|exists:re_currencies,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'country_id' => 'nullable|integer|exists:countries,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_featured' => 'nullable|boolean',
            'featured_priority' => 'nullable|integer|min:0|max:999',
            'auto_renew' => 'nullable|boolean',
            'never_expired' => 'nullable|boolean',
            'expire_date' => 'nullable|date|after:today',
            'private_notes' => 'nullable|string|max:1000',
            'floor_plans' => 'nullable|array',
            'floor_name' => 'nullable|string|max:255',
            'floor_plan_image' => 'nullable|string',
            'floor_plan_document' => 'nullable|string',
            
            // Vacation rental specific fields
            'check_in_time' => 'nullable|string',
            'check_out_time' => 'nullable|string',
            'minimum_stay' => 'nullable|integer|min:1|max:365',
            'maximum_stay' => 'nullable|integer|min:0|max:365',
            'maximum_guests' => 'nullable|integer|min:1|max:50',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'house_rules' => 'nullable|string|max:2000',
            'cancellation_policy' => 'nullable|string|in:flexible,moderate,strict,super_strict',
            
            // Relationships
            'categories' => 'nullable|array',
            'categories.*' => 'nullable|integer|exists:re_categories,id',
            'features' => 'nullable|array',
            'features.*' => 'nullable|integer|exists:re_features,id',
            'facilities' => 'nullable|array',
            'facilities.*.id' => 'nullable|integer|exists:re_facilities,id',
            'facilities.*.distance' => 'nullable|string|max:255',
            
            'status' => 'required|in:' . implode(',', VacationRentalStatusEnum::values()),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('plugins/real-estate::property.form.name'),
            'description' => trans('plugins/real-estate::property.form.description'),
            'content' => trans('plugins/real-estate::property.form.content'),
            'location' => trans('plugins/real-estate::property.form.location'),
            'images' => trans('plugins/real-estate::property.form.images'),
            'number_bedroom' => trans('plugins/real-estate::property.form.number_bedroom'),
            'number_bathroom' => trans('plugins/real-estate::property.form.number_bathroom'),
            'number_floor' => trans('plugins/real-estate::property.form.number_floor'),
            'square' => trans('plugins/real-estate::property.form.square'),
            'price' => trans('plugins/real-estate::property.form.price_per_night'),
            'currency_id' => trans('plugins/real-estate::property.form.currency'),
            'city_id' => trans('plugins/location::location.city'),
            'state_id' => trans('plugins/location::location.state'),
            'country_id' => trans('plugins/location::location.country'),
            'latitude' => trans('plugins/real-estate::property.form.latitude'),
            'longitude' => trans('plugins/real-estate::property.form.longitude'),
            'is_featured' => trans('core/base::forms.is_featured'),
            'featured_priority' => trans('plugins/real-estate::property.form.featured_priority'),
            'auto_renew' => trans('plugins/real-estate::property.form.auto_renew'),
            'never_expired' => trans('plugins/real-estate::property.never_expired'),
            'expire_date' => trans('plugins/real-estate::property.form.expire_date'),
            'private_notes' => trans('plugins/real-estate::property.private_notes'),
            'floor_plans' => trans('plugins/real-estate::property.form.floor_plans'),
            'floor_name' => trans('plugins/real-estate::property.form.floor_name'),
            'floor_plan_image' => trans('plugins/real-estate::property.form.floor_plan_image'),
            'floor_plan_document' => trans('plugins/real-estate::property.form.floor_plan_document'),
            
            // Vacation rental specific fields
            'check_in_time' => trans('plugins/real-estate::property.form.check_in_time'),
            'check_out_time' => trans('plugins/real-estate::property.form.check_out_time'),
            'minimum_stay' => trans('plugins/real-estate::property.form.minimum_stay'),
            'maximum_stay' => trans('plugins/real-estate::property.form.maximum_stay'),
            'maximum_guests' => trans('plugins/real-estate::property.form.maximum_guests'),
            'cleaning_fee' => trans('plugins/real-estate::property.form.cleaning_fee'),
            'security_deposit' => trans('plugins/real-estate::property.form.security_deposit'),
            'house_rules' => trans('plugins/real-estate::property.form.house_rules'),
            'cancellation_policy' => trans('plugins/real-estate::property.form.cancellation_policy'),
            
            // Relationships
            'categories' => trans('plugins/real-estate::property.form.categories'),
            'features' => trans('plugins/real-estate::property.form.features'),
            'facilities' => trans('plugins/real-estate::property.form.facilities'),
            
            'status' => trans('core/base::tables.status'),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('validation.required', ['attribute' => trans('plugins/real-estate::property.form.name')]),
            'name.max' => trans('validation.max.string', ['attribute' => trans('plugins/real-estate::property.form.name'), 'max' => 300]),
            'description.max' => trans('validation.max.string', ['attribute' => trans('plugins/real-estate::property.form.description'), 'max' => 400]),
            'location.max' => trans('validation.max.string', ['attribute' => trans('plugins/real-estate::property.form.location'), 'max' => 300]),
            'number_bedroom.integer' => trans('validation.integer', ['attribute' => trans('plugins/real-estate::property.form.number_bedroom')]),
            'number_bedroom.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::property.form.number_bedroom'), 'min' => 0]),
            'number_bedroom.max' => trans('validation.max.numeric', ['attribute' => trans('plugins/real-estate::property.form.number_bedroom'), 'max' => 100]),
            'number_bathroom.integer' => trans('validation.integer', ['attribute' => trans('plugins/real-estate::property.form.number_bathroom')]),
            'number_bathroom.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::property.form.number_bathroom'), 'min' => 0]),
            'number_bathroom.max' => trans('validation.max.numeric', ['attribute' => trans('plugins/real-estate::property.form.number_bathroom'), 'max' => 100]),
            'number_floor.integer' => trans('validation.integer', ['attribute' => trans('plugins/real-estate::property.form.number_floor')]),
            'number_floor.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::property.form.number_floor'), 'min' => 0]),
            'number_floor.max' => trans('validation.max.numeric', ['attribute' => trans('plugins/real-estate::property.form.number_floor'), 'max' => 100]),
            'square.numeric' => trans('validation.numeric', ['attribute' => trans('plugins/real-estate::property.form.square')]),
            'square.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::property.form.square'), 'min' => 0]),
            'price.numeric' => trans('validation.numeric', ['attribute' => trans('plugins/real-estate::property.form.price_per_night')]),
            'price.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::property.form.price_per_night'), 'min' => 0]),
            'currency_id.exists' => trans('validation.exists', ['attribute' => trans('plugins/real-estate::property.form.currency')]),
            'city_id.exists' => trans('validation.exists', ['attribute' => trans('plugins/location::location.city')]),
            'state_id.exists' => trans('validation.exists', ['attribute' => trans('plugins/location::location.state')]),
            'country_id.exists' => trans('validation.exists', ['attribute' => trans('plugins/location::location.country')]),
            'latitude.between' => trans('validation.between.numeric', ['attribute' => trans('plugins/real-estate::property.form.latitude'), 'min' => -90, 'max' => 90]),
            'longitude.between' => trans('validation.between.numeric', ['attribute' => trans('plugins/real-estate::property.form.longitude'), 'min' => -180, 'max' => 180]),
            'expire_date.after' => trans('validation.after', ['attribute' => trans('plugins/real-estate::property.form.expire_date'), 'date' => 'today']),
            
            // Vacation rental specific validation messages
            'check_in_time.date_format' => trans('plugins/real-estate::vacation-rental.validation.time_format', ['attribute' => trans('plugins/real-estate::property.form.check_in_time')]),
            'check_out_time.date_format' => trans('plugins/real-estate::vacation-rental.validation.time_format', ['attribute' => trans('plugins/real-estate::property.form.check_out_time')]),
            'minimum_stay.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::property.form.minimum_stay'), 'min' => 1]),
            'minimum_stay.max' => trans('validation.max.numeric', ['attribute' => trans('plugins/real-estate::property.form.minimum_stay'), 'max' => 365]),
            'maximum_stay.max' => trans('validation.max.numeric', ['attribute' => trans('plugins/real-estate::property.form.maximum_stay'), 'max' => 365]),
            'maximum_guests.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::property.form.maximum_guests'), 'min' => 1]),
            'maximum_guests.max' => trans('validation.max.numeric', ['attribute' => trans('plugins/real-estate::property.form.maximum_guests'), 'max' => 50]),
            'cleaning_fee.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::property.form.cleaning_fee'), 'min' => 0]),
            'security_deposit.min' => trans('validation.min.numeric', ['attribute' => trans('plugins/real-estate::property.form.security_deposit'), 'min' => 0]),
            'house_rules.max' => trans('validation.max.string', ['attribute' => trans('plugins/real-estate::property.form.house_rules'), 'max' => 2000]),
            'cancellation_policy.in' => trans('validation.in', ['attribute' => trans('plugins/real-estate::property.form.cancellation_policy')]),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate check-in time format
            if ($this->filled('check_in_time')) {
                if (!$this->isValidTimeFormat($this->input('check_in_time'))) {
                    $validator->errors()->add('check_in_time', trans('plugins/real-estate::vacation-rental.validation.time_format', [
                        'attribute' => trans('plugins/real-estate::property.form.check_in_time')
                    ]));
                }
            }

            // Validate check-out time format
            if ($this->filled('check_out_time')) {
                if (!$this->isValidTimeFormat($this->input('check_out_time'))) {
                    $validator->errors()->add('check_out_time', trans('plugins/real-estate::vacation-rental.validation.time_format', [
                        'attribute' => trans('plugins/real-estate::property.form.check_out_time')
                    ]));
                }
            }
        });
    }

    private function isValidTimeFormat($time): bool
    {
        if (empty($time)) {
            return true; // nullable field
        }

        // Remove any extra whitespace
        $time = trim($time);

        // Use regex for strict validation first
        if (preg_match('/^([0-1]?[0-9]|2[0-3]):([0-5][0-9])(:([0-5][0-9]))?$/', $time, $matches)) {
            $hour = (int) $matches[1];
            $minute = (int) $matches[2];

            // Ensure valid time ranges
            if ($hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59) {
                return true;
            }
        }

        return false;
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Normalize time formats to H:i
        if (isset($validated['check_in_time']) && !empty($validated['check_in_time'])) {
            $validated['check_in_time'] = $this->normalizeTimeFormat($validated['check_in_time']);
        }

        if (isset($validated['check_out_time']) && !empty($validated['check_out_time'])) {
            $validated['check_out_time'] = $this->normalizeTimeFormat($validated['check_out_time']);
        }

        return $validated;
    }

    private function normalizeTimeFormat($time): string
    {
        if (empty($time)) {
            return $time;
        }

        // Remove any extra whitespace
        $time = trim($time);

        // Try multiple formats and normalize to H:i
        $formats = ['H:i', 'G:i', 'H:i:s', 'G:i:s'];

        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $time);
            if ($parsed !== false) {
                return $parsed->format('H:i');
            }
        }

        return $time; // Return as-is if can't parse
    }
}

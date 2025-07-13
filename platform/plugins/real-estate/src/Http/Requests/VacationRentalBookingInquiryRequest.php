<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Rules\OnOffRule;
use Botble\Captcha\Facades\Captcha;
use Botble\RealEstate\Enums\ConsultCustomFieldTypeEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\ConsultCustomField;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\AvailabilityService;
use Botble\Support\Http\Requests\Request;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class VacationRentalBookingInquiryRequest extends Request
{
    protected Collection $customFields;

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:220'],
            'email' => ['nullable', 'email'],
            'phone' => 'nullable|string|' . BaseHelper::getPhoneValidationRule(),
            'content' => ['required', 'string'],
            'property_id' => ['required', 'exists:re_properties,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'guests_count' => ['required', 'integer', 'min:1', 'max:50'],
        ];

        if (is_plugin_active('captcha')) {
            $rules += Captcha::rules();
        }

        $availableMandatoryFields = RealEstateHelper::enabledMandatoryFieldsAtConsultForm();
        $hiddenFields = RealEstateHelper::getHiddenFieldsAtConsultForm();

        if ($hiddenFields) {
            Arr::forget($rules, $hiddenFields);
        }

        if ($availableMandatoryFields) {
            foreach ($availableMandatoryFields as $value) {
                if (! isset($rules[$value])) {
                    continue;
                }

                if (is_string($rules[$value])) {
                    $rules[$value] = str_replace('nullable', 'required', $rules[$value]);

                    continue;
                }

                if (is_array($rules[$value])) {
                    $rules[$value] = array_merge(['required'], array_filter($rules[$value], fn ($item) => $item !== 'nullable'));
                }
            }
        }

        $customFields = $this->getCustomFields();

        if ($customFields->isNotEmpty()) {
            $rules['consult_custom_fields'] = ['required', 'array'];
        }

        foreach ($customFields as $customField) {
            $customFieldRules = [$customField->required ? 'required' : 'nullable'];

            $rules["consult_custom_fields.$customField->id"] = match ($customField->type->getValue()) {
                ConsultCustomFieldTypeEnum::TEXT, ConsultCustomFieldTypeEnum::DROPDOWN, ConsultCustomFieldTypeEnum::RADIO, ConsultCustomFieldTypeEnum::DATE => [...$customFieldRules, 'string', 'max:255'],
                ConsultCustomFieldTypeEnum::TEXTAREA => [...$customFieldRules, 'string', 'max:1000'],
                ConsultCustomFieldTypeEnum::NUMBER => [...$customFieldRules, 'numeric'],
                ConsultCustomFieldTypeEnum::CHECKBOX => [new OnOffRule()],
                default => $customFieldRules,
            };
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate property is vacation rental
            if ($this->property_id) {
                $property = Property::find($this->property_id);
                if (!$property || $property->type !== 'vacation_rental') {
                    $validator->errors()->add('property_id', 'Property must be a vacation rental.');
                }
            }

            // Validate availability
            if ($this->property_id && $this->check_in_date && $this->check_out_date) {
                $availabilityService = app(AvailabilityService::class);
                $checkIn = Carbon::parse($this->check_in_date);
                $checkOut = Carbon::parse($this->check_out_date);

                if (!$availabilityService->checkAvailability($this->property_id, $checkIn, $checkOut)) {
                    $validator->errors()->add('check_in_date', 'Selected dates are not available.');
                }

                // Validate minimum stay
                if (!$availabilityService->validateMinimumStay($this->property_id, $checkIn, $checkOut)) {
                    $effectiveMinimumStay = $availabilityService->getEffectiveMinimumStay($this->property_id, $checkIn);
                    $validator->errors()->add('check_out_date', "Minimum stay is {$effectiveMinimumStay} nights.");
                }
            }

            // Validate guest count against property capacity
            if ($this->property_id && $this->guests_count) {
                $property = Property::find($this->property_id);
                if ($property && $property->maximum_guests && $this->guests_count > $property->maximum_guests) {
                    $validator->errors()->add('guests_count', "Maximum {$property->maximum_guests} guests allowed.");
                }
            }
        });
    }

    public function attributes(): array
    {
        $attributes = [
            'name' => __('Name'),
            'phone' => __('Phone'),
            'email' => __('Email'),
            'content' => __('Message'),
            'property_id' => __('Property'),
            'check_in_date' => __('Check-in Date'),
            'check_out_date' => __('Check-out Date'),
            'guests_count' => __('Number of Guests'),
        ] + (is_plugin_active('captcha') ? Captcha::attributes() : []);

        $customFields = $this->getCustomFields();

        foreach ($customFields as $customField) {
            $attributes["consult_custom_fields.$customField->id"] = $customField->name;
        }

        return $attributes;
    }

    protected function getCustomFields(): Collection
    {
        if (isset($this->customFields)) {
            return $this->customFields;
        }

        return $this->customFields = ConsultCustomField::query()
            ->wherePublished()
            ->with('options')
            ->get();
    }
}

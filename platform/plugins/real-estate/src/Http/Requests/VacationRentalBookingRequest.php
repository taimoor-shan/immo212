<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Carbon\Carbon;

class VacationRentalBookingRequest extends Request
{
    public function rules(): array
    {
        return [
            'property_id' => 'required|exists:re_properties,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests_count' => 'required|integer|min:1|max:20',

            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'special_requests' => 'nullable|string|max:1000',
            'payment_method' => 'required|string',
            'terms_accepted' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'property_id.required' => __('Property is required.'),
            'property_id.exists' => __('Selected property is invalid.'),
            'check_in_date.required' => __('Check-in date is required.'),
            'check_in_date.date' => __('Check-in date must be a valid date.'),
            'check_in_date.after_or_equal' => __('Check-in date must be today or later.'),
            'check_out_date.required' => __('Check-out date is required.'),
            'check_out_date.date' => __('Check-out date must be a valid date.'),
            'check_out_date.after' => __('Check-out date must be after check-in date.'),
            'guests_count.required' => __('Number of guests is required.'),
            'guests_count.integer' => __('Number of guests must be a number.'),
            'guests_count.min' => __('At least 1 guest is required.'),
            'guests_count.max' => __('Maximum 20 guests allowed.'),

            'guest_name.required' => __('Guest name is required.'),
            'guest_name.string' => __('Guest name must be text.'),
            'guest_name.max' => __('Guest name cannot exceed 255 characters.'),
            'guest_email.required' => __('Guest email is required.'),
            'guest_email.email' => __('Guest email must be a valid email address.'),
            'guest_email.max' => __('Guest email cannot exceed 255 characters.'),
            'guest_phone.string' => __('Guest phone must be text.'),
            'guest_phone.max' => __('Guest phone cannot exceed 20 characters.'),
            'special_requests.string' => __('Special requests must be text.'),
            'special_requests.max' => __('Special requests cannot exceed 1000 characters.'),
            'payment_method.required' => __('Payment method is required.'),
            'payment_method.string' => __('Payment method must be text.'),
            'terms_accepted.required' => __('You must accept the terms and conditions.'),
            'terms_accepted.accepted' => __('You must accept the terms and conditions.'),
        ];
    }

    public function attributes(): array
    {
        return [
            'property_id' => __('Property'),
            'check_in_date' => __('Check-in date'),
            'check_out_date' => __('Check-out date'),
            'guests_count' => __('Number of guests'),

            'guest_name' => __('Guest name'),
            'guest_email' => __('Guest email'),
            'guest_phone' => __('Guest phone'),
            'special_requests' => __('Special requests'),
            'payment_method' => __('Payment method'),
            'terms_accepted' => __('Terms and conditions'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $checkInDate = $this->input('check_in_date');
            $checkOutDate = $this->input('check_out_date');

            if ($checkInDate && $checkOutDate) {
                try {
                    $checkIn = Carbon::parse($checkInDate);
                    $checkOut = Carbon::parse($checkOutDate);

                    if (!$checkOut->gt($checkIn)) {
                        $validator->errors()->add('check_out_date', __('Check-out date must be after check-in date.'));
                    }
                } catch (\Exception $e) {
                    // If date parsing fails, let the regular date validation handle it
                }
            }
        });
    }
}

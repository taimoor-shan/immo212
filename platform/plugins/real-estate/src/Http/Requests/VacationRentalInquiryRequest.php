<?php

namespace Botble\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VacationRentalInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'integer', 'exists:re_properties,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'check_in_date' => ['required', 'date', 'after:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'guests_count' => ['required', 'integer', 'min:1', 'max:50'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'property_id.required' => __('Property is required.'),
            'property_id.exists' => __('Selected property does not exist.'),
            'name.required' => __('Name is required.'),
            'email.required' => __('Email is required.'),
            'email.email' => __('Please enter a valid email address.'),
            'check_in_date.required' => __('Check-in date is required.'),
            'check_in_date.after' => __('Check-in date must be in the future.'),
            'check_out_date.required' => __('Check-out date is required.'),
            'check_out_date.after' => __('Check-out date must be after check-in date.'),
            'guests_count.required' => __('Number of guests is required.'),
            'guests_count.min' => __('At least 1 guest is required.'),
            'guests_count.max' => __('Maximum 50 guests allowed.'),
            'message.max' => __('Message cannot exceed 1000 characters.'),
        ];
    }
}

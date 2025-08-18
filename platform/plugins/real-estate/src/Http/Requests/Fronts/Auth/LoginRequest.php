<?php

namespace Botble\RealEstate\Http\Requests\Fronts\Auth;

use Botble\Base\Rules\EmailRule;
use Botble\Support\Http\Requests\Request;

class LoginRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $email = $this->input('email');

            // If it looks like an email, validate it as an email
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailRule = new EmailRule();
                $emailRule->validate('email', $email, function ($message) use ($validator) {
                    $validator->errors()->add('email', $message);
                });
            }
            // If it's not an email format, treat it as username (no additional validation needed)
        });
    }

    public function messages(): array
    {
        return [
            'email.required' => __('Email or username is required.'),
            'password.required' => __('Password is required.'),
        ];
    }
}

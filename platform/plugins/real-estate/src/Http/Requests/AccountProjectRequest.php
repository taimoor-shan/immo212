<?php

namespace Botble\RealEstate\Http\Requests;

class AccountProjectRequest extends ProjectRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        // Ensure content is required on frontend
        $rules['content'] = ['required', 'string', 'max:300000'];

        // Remove fields users shouldn't control if present
        unset($rules['author_id'], $rules['author_type']);

        return $rules;
    }
}

<?php

namespace VigStudio\VigAutoTranslations\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'vig_translate_driver' => ['required'],
            'vig_translate_aws_key' => ['required_if:vig_translate_driver,aws'],
            'vig_translate_aws_secret' => ['required_if:vig_translate_driver,aws'],
            'vig_translate_aws_region' => ['required_if:vig_translate_driver,aws'],
            'vig_translate_chatgpt_key' => ['required_if:vig_translate_driver,chatgpt'],
            'vig_translate_chatgpt_model' => ['nullable', 'string', 'in:gpt-4.1,gpt-4.1-mini,gpt-4.1-nano,gpt-4o,gpt-4-turbo,gpt-4,gpt-3.5-turbo'],
            'vig_translate_chatgpt_system_message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

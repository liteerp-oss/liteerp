<?php

namespace Extensions\PusherSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavePusherSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'app_id' => 'nullable|string|max:255',
            'app_key' => 'nullable|string|max:255',
            'app_secret' => 'nullable|string|max:255',
            'cluster' => 'nullable|string|max:100',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'scheme' => 'nullable|in:https,http',
            'enabled' => 'nullable|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace Extensions\Smtp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to' => ['required', 'email', 'max:255'],

            'subject' => ['required', 'string', 'max:255'],

            'message' => ['required', 'string'],
        ];
    }
}

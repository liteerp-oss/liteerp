<?php

namespace Extensions\Smtp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'host' => ['required', 'string', 'max:255'],

            'port' => ['required', 'integer', 'between:1,65535'],

            'username' => ['required', 'string', 'max:255'],

            'password' => ['required', 'string', 'max:255'],

            'encryption' => ['required', 'in:tls,ssl'],

            'from_email' => ['required', 'email', 'max:255'],

            'from_name' => ['required', 'string', 'max:255'],
        ];
    }
}

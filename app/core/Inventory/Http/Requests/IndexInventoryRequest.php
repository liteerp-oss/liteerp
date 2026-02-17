<?php

namespace Core\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexInventoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'keywords' => 'nullable|string|max:150',
            'order_by' => 'nullable|in:ASC,DESC'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

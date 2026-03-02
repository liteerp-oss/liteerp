<?php

namespace Core\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexInventoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'keywords' => 'nullable|string|max:150',
            'order_by' => 'nullable|in:ASC,DESC',
            'order_id' => 'nullable|exists:orders,id'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

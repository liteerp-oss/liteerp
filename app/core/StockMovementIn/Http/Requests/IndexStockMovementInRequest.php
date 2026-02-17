<?php

namespace Core\StockMovementIn\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexStockMovementInRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'stock_in_id'  => 'required|numeric|exists:stock_ins,id',
            'keywords'     => 'nullable|string|max:150',
            'order_by'     => 'nullable|in:ASC,DESC'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
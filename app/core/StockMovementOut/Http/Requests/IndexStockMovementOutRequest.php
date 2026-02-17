<?php

namespace Core\StockMovementOut\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexStockMovementOutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'stock_out_id' => 'required|integer|exists:stock_outs,id',
            'keywords' => 'nullable|string|max:150',
            'order_by' => 'nullable|in:ASC,DESC'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

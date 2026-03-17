<?php

namespace Core\StockMovementIn\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class IndexStockMovementInRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            'stock_in_id'  => 'nullable|numeric|exists:stock_ins,id',
            'keywords'     => 'nullable|string|max:150',
            'order_by'     => 'nullable|in:ASC,DESC',
            'customer_id' => 'nullable|numeric|exists:customers,id',
            'purchase_id' => 'nullable|numeric|exists:purchases,id',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::INDEX,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'StockMovementIn'
                )
            )
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace Core\StockMovementIn\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class CreateStockMovementInRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            'product_id' => 'required|numeric|exists:products,id',
            'warehouse_id' => 'required|numeric|exists:warehouses,id',
            'qty_change'   => 'required|numeric|min:1',
            'stock_in_id'  => 'required|numeric|exists:stock_ins,id',
            'purchase_item_id' => 'required|exists:purchase_items,id',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::CREATE,
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

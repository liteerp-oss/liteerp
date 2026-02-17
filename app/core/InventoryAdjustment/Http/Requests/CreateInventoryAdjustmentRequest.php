<?php

namespace Core\InventoryAdjustment\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class CreateInventoryAdjustmentRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            'product_id'   => 'required|integer|exists:products,id',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'qty_adjusted' => 'required|numeric',
            'reason'       => 'required|string|max:250',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::CREATE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'InventoryAdjustment'
                )
            )
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
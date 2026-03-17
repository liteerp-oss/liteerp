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
            'stock_movements_in_id' => 'required|integer|exists:stock_movements_in,id',
            'qty_adjusted' => 'required|numeric|max:-1|lt:0',
            'reason'       => 'required|string|max:250',
            'purchase_id'  => 'required|exists:purchases,id',
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
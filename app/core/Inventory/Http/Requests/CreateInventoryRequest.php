<?php

namespace Core\Inventory\Http\Requests;

use App\Supports\Hooks\HookDispatcher;
use Illuminate\Foundation\Http\FormRequest;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
class CreateInventoryRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        $hooks = $hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::VALIDATE,
                timing: HookTiming::ON,
                payload: [],
                module: 'Inventory'
            )
        );
        return [
            'product_id'   => 'required|integer|exists:products,id',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'quantity'     => 'nullable|numeric|min:0',
            'reserved_qty' => 'nullable|numeric|min:0',
            'stock_in_id'  => 'nullable|exists:stock_ins,id',
            'stock_out_id'  => 'nullable|exists:stock_outs,id',
            ...$hooks
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

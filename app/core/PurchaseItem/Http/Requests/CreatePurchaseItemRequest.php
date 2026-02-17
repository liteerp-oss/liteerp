<?php

namespace Core\PurchaseItem\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class CreatePurchaseItemRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            'discount'                => 'nullable|integer|min:0',
            'tax'                     => 'nullable|required|integer|min:0|max:100',
            'product_link'            => 'nullable|string|max:250',
            'buy_quantity'            => 'nullable|integer|min:0',
            'gift_quantity'           => 'nullable|integer|min:0',
            'compensation_quantity'   => 'nullable|integer|min:0',
            'conversion_quantity'     => 'nullable|integer|min:0',
            'unit_cost'               => 'nullable|integer|min:0',
            'purchase_id'             => 'required|exists:purchases,id,deleted_at,NULL',
            'product_id'              => 'required|exists:products,id,deleted_at,NULL',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::CREATE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'PurchaseItem'
                )
            )
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $buy  = (int) $this->input('buy_quantity', 0);
            $gift = (int) $this->input('gift_quantity', 0);
            $comp = (int) $this->input('compensation_quantity', 0);
            $conv = (int) $this->input('conversion_quantity', 0);

            // Check: at least one quantity > 0
            if ($buy <= 0 && $gift <= 0 && $comp <= 0 && $conv <= 0) {
                $validator->errors()->add(
                    'quantity',
                    'At least one quantity field must be greater than 0: buy_quantity, gift_quantity, compensation_quantity, or conversion_quantity.'
                );
            }
        });
    }

    public function authorize(): bool
    {
        return true;
    }
}

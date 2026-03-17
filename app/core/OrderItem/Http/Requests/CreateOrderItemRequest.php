<?php

namespace Core\OrderItem\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderItemRequest extends FormRequest
{
     public function rules(HookDispatcher $hooks): array
    {
        return [
            'order_id'               => 'required|integer|exists:orders,id',
            'stock_movements_in_id'           => 'required|integer|exists:stock_movements_in,id',
            'discount'               => 'nullable|numeric|min:0',

            'buy_quantity'           => 'nullable|numeric|min:0',
            'gift_quantity'          => 'nullable|numeric|min:0',
            'compensation_quantity'  => 'nullable|numeric|min:0',
            'conversion_quantity'    => 'nullable|numeric|min:0',

            'price'                  => 'required|numeric|min:0',
            'tax'                    => 'required|numeric|min:0',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::CREATE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'OrderItem'
                )
            )
        ];
    }

    /**
     * Custom quantity validation rule
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $buy  = (float) ($this->buy_quantity ?? 0);
            $gift = (float) ($this->gift_quantity ?? 0);
            $comp = (float) ($this->compensation_quantity ?? 0);
            $conv = (float) ($this->conversion_quantity ?? 0);

            if ($buy <= 0 && $gift <= 0 && $comp <= 0 && $conv <= 0) {
                $validator->errors()->add(
                    'quantity',
                    'At least one quantity field must be greater than 0.'
                );
            }
        });
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace Core\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Hooks\HookDispatcher;

class UpdateOrderRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        // Dispatch the hooks to get additional validation rules
        $additionalRules = $hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::VALIDATE,
                timing: HookTiming::ON,
                payload: [],
                module: 'Order'
            )
        );

        return [
            ...$additionalRules, // Merge additional rules from hooks
            'order_date' => 'date',
            'expected_delivery_date' => 'date|after_or_equal:order_date',
            'order_no' => 'required|string|max:150',
            'note' => 'nullable|string',

            'type' => 'required|in:retail,wholesale',
            'status' => 'required|in:pending,approved,cancelled',
            'reason' => 'nullable|string|max:250',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}


<?php

namespace Core\Shipping\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class DeleteShippingRequest extends FormRequest
{
     public function rules(HookDispatcher $dispatch): array
    {
        return [
            ...$dispatch->dispatch(
                new HookContext(
                    action: HookAction::DELETE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'Shipping'
                )
            )
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
<?php

namespace Core\Shipping\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class IndexShippingRequest extends FormRequest
{
     public function rules(HookDispatcher $dispatch): array
    {
        return [
            'keywords' => 'nullable|string|max:150',
            'active'   => 'nullable|boolean',
            'order_by' => 'nullable|in:ASC,DESC',
            ...$dispatch->dispatch(
                new HookContext(
                    action: HookAction::INDEX,
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
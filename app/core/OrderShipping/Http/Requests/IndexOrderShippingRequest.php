<?php

namespace Core\OrderShipping\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class IndexOrderShippingRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        $hookRules = $hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::VALIDATE,
                timing: HookTiming::ON,
                payload: [],
                module: 'OrderShipping'
            )
        );

        return [
            ...$hookRules,
            'order_by' => 'nullable|in:ASC,DESC',
            'keywords' => 'nullable|string|max:150',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

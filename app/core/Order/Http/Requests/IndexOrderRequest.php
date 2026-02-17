<?php

namespace Core\Order\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class IndexOrderRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        $additionalRules = $hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::VALIDATE,
                timing: HookTiming::ON,
                payload: [],
                module: 'Order'
            )
        );

        return [
            ...$additionalRules, 
            'keywords' => 'nullable|string|max:150',
            'order_by' => 'nullable|in:ASC,DESC'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

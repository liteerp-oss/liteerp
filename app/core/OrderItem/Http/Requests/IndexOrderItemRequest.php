<?php

namespace Core\OrderItem\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class IndexOrderItemRequest extends FormRequest
{
     public function rules(HookDispatcher $hooks): array
    {
        return [
            'order_by' => 'nullable|in:ASC,DESC',
            'keywords'  => 'nullable|string|max:150',
            'order_id' => 'required|numeric|exists:orders,id',
            'summary' => 'nullable|boolean',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::INDEX,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'OrderItem'
                )
            )
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

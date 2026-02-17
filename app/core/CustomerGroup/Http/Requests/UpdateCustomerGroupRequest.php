<?php

namespace Core\CustomerGroup\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerGroupRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            'business_id' => 'required|integer|exists:business,id',
            'name'        => 'required|string|max:255',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::UPDATE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'CustomerGroup'
                )
            )
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace Core\User\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            'email' => 'required|string|email|max:150',
            'group_id' => 'required|exists:permission_groups,id',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::UPDATE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'User'
                )
            )
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

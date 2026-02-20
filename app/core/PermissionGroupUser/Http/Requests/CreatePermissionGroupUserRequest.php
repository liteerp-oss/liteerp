<?php

namespace Core\PermissionGroupUser\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class CreatePermissionGroupUserRequest extends FormRequest
{
    public function rules(HookDispatcher $dispatch): array
    {
        return [
            'group_id' => 'required|integer|exists:permission_groups,id',
            'user_id' => 'required|integer|exists:users,id',
            ...$dispatch->dispatch(
                new HookContext(
                    action: HookAction::CREATE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'PermissionGroupUser'
                )
            ),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

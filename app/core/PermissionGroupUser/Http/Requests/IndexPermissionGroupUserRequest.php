<?php

namespace Core\PermissionGroupUser\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class IndexPermissionGroupUserRequest extends FormRequest
{
    public function rules(HookDispatcher $dispatch): array
    {
        return [
            'group_id' => 'nullable|integer|exists:permission_groups,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'order_by' => 'nullable|in:ASC,DESC',
            ...$dispatch->dispatch(
                new HookContext(
                    action: HookAction::INDEX,
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

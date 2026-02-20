<?php

namespace Core\Permission\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class CreatePermissionRequest extends FormRequest
{
    public function rules(HookDispatcher $dispatch): array
    {
        return [
            'group_id' => 'required|numeric|exists:permission_groups,id',
            'permissions' => 'required|array',
            'permissions.*' => 'required|string|regex:/^erp\.[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]+$/',
            ...$dispatch->dispatch(
                new HookContext(
                    action: HookAction::CREATE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'Permission'
                )
            ),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

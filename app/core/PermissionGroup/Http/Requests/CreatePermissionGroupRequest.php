<?php

namespace Core\PermissionGroup\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class CreatePermissionGroupRequest extends FormRequest
{
    public function rules(HookDispatcher $dispatch): array
    {
        return [
            'name' => 'required|string|max:100',
            ...$dispatch->dispatch(
                new HookContext(
                    action: HookAction::CREATE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'PermissionGroup'
                )
            ),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

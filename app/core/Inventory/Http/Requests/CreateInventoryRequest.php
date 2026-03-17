<?php

namespace Core\Inventory\Http\Requests;

use App\Supports\Hooks\HookDispatcher;
use Illuminate\Foundation\Http\FormRequest;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
class CreateInventoryRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        $hooks = $hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::VALIDATE,
                timing: HookTiming::ON,
                payload: [],
                module: 'Inventory'
            )
        );
        return [
            ...$hooks
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

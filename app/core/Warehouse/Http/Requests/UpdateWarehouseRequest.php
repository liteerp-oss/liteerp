<?php

namespace Core\Warehouse\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            'name' => 'required|string|max:150',
            'address' => 'required|string|max:255',
            'active'  => 'nullable|boolean',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::UPDATE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'Warehouse'
                )
            )
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

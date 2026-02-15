<?php

namespace Core\CategoryProduct\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryProductRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::UPDATE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'CategoryProduct'
                )
            ),
            'name' => 'required|string|max:150',
            'description' => 'required|string|max:250',
            'tax' => 'required|numeric|min:0|max:100'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
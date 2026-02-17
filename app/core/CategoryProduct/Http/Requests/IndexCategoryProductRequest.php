<?php

namespace Core\CategoryProduct\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class IndexCategoryProductRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            'keywords' => 'nullable|string|max:150',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::INDEX,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'CategoryProduct'
                )
            ),
            'order_by' => 'nullable|in:ASC,DESC',
        ];
    }
    public function authorize(): bool
    {
        return true;
    }
}

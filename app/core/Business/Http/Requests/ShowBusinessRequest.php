<?php

namespace Core\Business\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
class ShowBusinessRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            ...$hooks->dispatch(new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::VALIDATE,
                timing: HookTiming::ON,
                module: 'Business',
                payload: []
            ))
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
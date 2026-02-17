<?php

namespace Core\InvoiceIn\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class IndexInvoiceInRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            'keywords' => 'nullable|string|max:150',
            'order_by' => 'nullable|in:DESC,ASC',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::INDEX,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'InvoiceIn'
                )
            )
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

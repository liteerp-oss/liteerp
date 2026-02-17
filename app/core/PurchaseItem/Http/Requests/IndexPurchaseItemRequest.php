<?php

namespace Core\PurchaseItem\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class IndexPurchaseItemRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            'purchase_id' => 'required|exists:purchases,id',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::INDEX,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'PurchaseItem'
                )
            ),
            'order_by' => 'nullable|in:ASC,DESC',
            'keywords' => 'nullable|string|max:150',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

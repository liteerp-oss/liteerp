<?php

namespace Core\Product\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        $hooks = $hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::VALIDATE,
                timing: HookTiming::ON,
                payload: [],
                module: 'Product'
            )
        );
        return [
            ...$hooks,
            'category_id'        => 'required|exists:category_product,id,deleted_at,NULL',
            'sku'                => 'required|string|max:100',
            'name'               => 'required|string|max:255',
            'unit'               => 'required|in:pcs,set,box,carton,bag,pack,roll',

            'description'        => 'nullable|string|max:4096|min:150',
            'image'              => 'nullable|string|max:255',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

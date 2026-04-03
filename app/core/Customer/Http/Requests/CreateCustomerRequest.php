<?php

namespace Core\Customer\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Hooks\HookDispatcher;
use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        $hooks = $hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::VALIDATE,
                timing: HookTiming::ON,
                payload: [],
                module: 'Customer'
            )
        );
        return [
            ...$hooks,
            'name'          => 'required|string|max:255',
            'contact_name'  => 'nullable|string|max:255',

            'email'         => 'nullable|email|max:255',
            'phone'         => 'required|string|max:50',
            'address'       => 'nullable|string|max:255',

            'tax_code'      => 'nullable|string|max:100',
            'national_id'   => 'nullable|string|max:100',
            'bank_name'     => 'nullable|string|max:255',
            'bank_account'  => 'nullable|string|max:255',

            'type'          => 'required|in:individual,company',

            'group'         => 'required|exists:customer_group,id',
            'website'       => 'nullable|url|max:255',

            'note'          => 'nullable|string'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace Core\Business\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        return [
            'name' => 'required|string|max:150',
            'address' => 'required|string|max:250',
            'tax_code'  => 'required|string|max:200',
            'phone'  => 'required|string|max:12',
            'email'  => 'required|email|max:150',
            'logo_url'  => 'nullable|string|max:250',
            'bank_name'  => 'required|string|max:200',
            'bank_account_number'  => 'required|string|max:200',
            'bank_account_name'  => 'required|string|max:200',
            ...$hooks->dispatch(new HookContext(
                action: HookAction::UPDATE,
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
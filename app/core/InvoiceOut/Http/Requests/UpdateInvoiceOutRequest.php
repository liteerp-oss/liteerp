<?php

namespace Core\InvoiceOut\Http\Requests;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Foundation\Http\FormRequest;
use Core\InvoiceOut\Application\DTOs\CreateInvoiceOutRequest as CreateInvoiceOutDTO;

class UpdateInvoiceOutRequest extends FormRequest
{
    public function rules(HookDispatcher $hooks): array
    {
        $id = $this->route('invoice_out');
        return [
            'document_no'  => 'required|string|max:255|unique:invoice_outs,document_no,' . $id,

            'payment_status'       => 'required|in:paid,partial_payment,pending',

            'invoice_date' => 'required|date_format:Y-m-d',
            'due_date'     => 'nullable|date_format:Y-m-d',
            'approved'     => 'required|boolean',
            'image'        => 'nullable|string|max:255',
            'amount_paid'  => 'nullable|numeric|min:0',
            ...$hooks->dispatch(
                new HookContext(
                    action: HookAction::UPDATE,
                    phase: HookPhase::VALIDATE,
                    timing: HookTiming::ON,
                    payload: [],
                    module: 'InvoiceOut'
                )
            )  
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace Extensions\FastMode\Hooks;

use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Extensions\FastMode\Jobs\TriggerApproveInvoice;
use Extensions\FastMode\Models\FastModeModel;

class TriggerUpdateInvoiceHook implements HookInterface
{
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::CREATE
            && $context->phase === HookPhase::RESPONSE
            && $context->timing === HookTiming::AFTER
            && $context->module === 'InvoiceOut';
    }

    public function handle(HookContext $context): HookResult
    {
        if (empty($context->payload['business_id'])) {
            return HookResult::pass([
                ...$context->payload
            ]);
        }
        $setting = FastModeModel::where('business_id', $context->payload['business_id'])->first();
        if (!$setting) {
            return HookResult::pass([
                ...$context->payload
            ]);
        }
        TriggerApproveInvoice::dispatch([
            ...$context->payload,
            'invoice_date' => date('Y-m-d', time()),
            'due_date' => date('Y-m-d', time()),
            'payment_status' => $setting->status,
            'approved' => true
        ]);
        return HookResult::pass([
            ...$context->payload
        ]);
    }
}

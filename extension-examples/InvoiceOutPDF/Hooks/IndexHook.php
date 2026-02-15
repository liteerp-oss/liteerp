<?php

namespace Extensions\InvoiceOutPDF\Hooks;

use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Illuminate\Support\Facades\DB;

class IndexHook implements HookInterface
{
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::INDEX
            && $context->phase === HookPhase::QUERY
            && $context->module === 'InvoiceOut'
            && $context->timing === HookTiming::ON;
    }

    public function handle(HookContext $context): HookResult
    {
        $context->payload['query'] = $context->payload['query']
            ->addSelect(DB::raw("CONCAT('" . route('InvoiceOutPDF') . "/', invoice_outs.id) AS pdf"));

        return HookResult::pass($context->payload);
    }
}

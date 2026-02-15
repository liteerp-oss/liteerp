<?php

namespace Extensions\InvoiceOutPDF\Hooks;

use App\Contracts\Hooks\HookInterface;
use App\Supports\Forms\IndexFieldRender;
use App\Supports\Forms\IndexFieldType;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;

class IndexShowHook implements HookInterface
{
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::INDEX
            && $context->phase === HookPhase::UI
            && $context->module === 'InvoiceOut'
            && $context->timing === HookTiming::ON;
    }

    public function handle(HookContext $context): HookResult
    {
        $th = new IndexFieldRender(
            type: IndexFieldType::LINK,
            key: 'pdf',
            label: 'PDF'
        );

        return HookResult::pass([
            ...$context->payload,
            $th->toArray(),
        ]);
    }
}

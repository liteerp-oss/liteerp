<?php

namespace Extensions\InventoryTracking\Hooks;

use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Extensions\InventoryTracking\Jobs\InventoryTrackingJob;

class TrackingHook implements HookInterface
{
    private static $module = "Inventory";
    public function __construct() {}
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::UPDATE
            && $context->phase === HookPhase::RESPONSE
            && $context->timing === HookTiming::AFTER
            && $context->module === self::$module;
    }

    public function handle(HookContext $context): HookResult
    {
        InventoryTrackingJob::dispatch($context->payload);
        return HookResult::pass([
            ...$context->payload,
        ]);
    }
}

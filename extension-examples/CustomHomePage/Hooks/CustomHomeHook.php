<?php

namespace Extensions\CustomHomePage\Hooks;

use App\Supports\Forms\FormFieldRender;
use App\Supports\Forms\FormFieldType;
use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;

class CustomHomeHook implements HookInterface
{
    private static $module="Home";
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::INDEX
            && $context->phase === HookPhase::UI
            && $context->timing === HookTiming::ON
            && $context->module === self::$module;
    }

    public function handle(HookContext $context): HookResult
    {
        $context->payload['view'] = 'extension.customhomepage::index';
        return HookResult::pass([
            ...$context->payload
        ]);
    }
}
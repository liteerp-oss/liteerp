<?php

namespace Extensions\InventoryTracking\Hooks;

use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Core\BusinessRole\Infrastructure\Helpers\SupportUINav;

class AddMenuHook implements HookInterface
{
    private static $module="BusinessRole";
    private string $action = 'erp.inventorytracking.index';
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::INDEX
            && $context->phase === HookPhase::UI
            && $context->timing === HookTiming::BEFORE
            && $context->module === self::$module;
    }

    public function handle(HookContext $context): HookResult
    {
        $nav = [
            ...$context->payload['nav'],
            SupportUINav::buildNavItem([
                'to'        => '/inventorytracking',
                'link'      => null,
                'icon'      => "bi bi-person-workspace",
                'label'     => __("extension.inventorytracking::messages.nav"),
                'ability'   => $this->action,
        ])
        ];
        return HookResult::pass([
            ...$context->payload,
            'roles' => [
            ...$context->payload['roles'],
            $this->action
            ],
            'nav' => $nav
        ]);
    }
}
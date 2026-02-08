<?php

namespace Extensions\PusherSetting\Hooks;

use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Core\BusinessRole\Infrastructure\Helpers\SupportUINav;

class AddNavMenu implements HookInterface
{
    private string $action = 'erp.pusher.setting.index';

    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::INDEX
            && $context->phase === HookPhase::UI
            && $context->module === 'BusinessRole'
            && $context->timing === HookTiming::ON;
    }

    public function handle(HookContext $context): HookResult
    {
        $nav = [
            ...$context->payload['nav'],
            SupportUINav::buildNavItem([
                'to'        => '/pusher-setting',
                'link'      => null,
                'icon'      => "bi bi-broadcast",
                'label'     => "Pusher Setting",
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

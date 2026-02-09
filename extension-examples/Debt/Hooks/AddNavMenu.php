<?php

namespace Extensions\Debt\Hooks;

use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Core\BusinessRole\Infrastructure\Helpers\SupportUINav;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
class AddNavMenu implements HookInterface
{
    private string $action = 'erp.debt.index';
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::INDEX
            && $context->phase === HookPhase::UI
            && $context->module === 'BusinessRole'
            && $context->timing === HookTiming::BEFORE;
    }

    public function handle(HookContext $context): HookResult
    {
        $token = Str::random(32);
        Cache::set($token,true);
        $nav = [
            ...$context->payload['nav'],
            SupportUINav::buildNavItem([
                'to'        => null,
                'link'      => route('debt.overview'),
                'icon'      => "bi bi-journal-bookmark",
                'label'     => __("debt::message.debt"),
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

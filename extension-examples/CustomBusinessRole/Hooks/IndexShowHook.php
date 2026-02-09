<?php

namespace Extensions\CustomBusinessRole\Hooks;

use App\Supports\Forms\IndexFieldType;
use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Forms\IndexFieldRender;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Core\BusinessRole\Infrastructure\Helpers\SupportUINav;

class IndexShowHook implements HookInterface
{
    private string $action = 'erp.overview.index';
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::INDEX
            && $context->phase === HookPhase::UI
            && $context->module === 'BusinessRole'
            && $context->timing === HookTiming::BEFORE;
    }

    public function handle(HookContext $context): HookResult
    {
        $has = false;
        foreach($context->payload['nav'] as $key => $value ) {
            if($value['ability'] === $this->action) {
                $has = true;
            }
        }
        $nav = $context->payload['nav'];
        if(!$has) {
            $nav = [
                SupportUINav::openNav($this->action),
                ...$context->payload['nav'],
            ];
        }
        return HookResult::pass([
            'roles' => [
                ...$context->payload['roles'],
                $this->action
            ],
            'nav' => $nav
        ]);
    }
}
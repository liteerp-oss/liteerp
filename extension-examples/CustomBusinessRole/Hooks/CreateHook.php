<?php

namespace Extensions\CustomBusinessRole\Hooks;
use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Extensions\CustomBusinessRole\Models\CustomBusinessRoleModel;

class CreateHook implements HookInterface
{
    private string $action = 'erp.overview.index';
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::CREATE
            && $context->phase === HookPhase::RESPONSE
            && $context->module === 'BusinessRole'
            && $context->timing === HookTiming::BEFORE;
    }

    public function handle(HookContext $context): HookResult
    {
        if(in_array($this->action,$context->payload['roles']) ) {
            return HookResult::pass([
                ...$context->payload
            ]);
        }
        CustomBusinessRoleModel::create([
            'role' => 'erp.overview.index',
            'business_role_id' => $context->payload['business_role']['id']
        ]);
        return HookResult::pass([
            ...$context->payload
        ]);
    }
}
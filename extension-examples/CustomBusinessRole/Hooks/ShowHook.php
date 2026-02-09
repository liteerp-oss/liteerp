<?php

namespace Extensions\CustomBusinessRole\Hooks;

use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Extensions\CustomBusinessRole\Models\CustomBusinessRoleModel;

class ShowHook implements HookInterface
{
    private string $action = 'erp.overview.index';
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::SHOW
               && $context->phase === HookPhase::RESPONSE
               && $context->module === 'BusinessRole'
               && $context->timing === HookTiming::BEFORE;
    }

    public function handle(HookContext $context): HookResult
    {
        if(!empty($context->payload['action']) && 
            $context->payload['action'] !== $this->action) {
            return HookResult::pass([
                ...$context->payload
            ]);
        }
        if(!empty($context->payload['action']) && 
            $context->payload['action'] === $this->action 
                && in_array($this->action,$context->payload['roles']) ) {
            return HookResult::pass([
                ...$context->payload
            ]);
        }
        $row = CustomBusinessRoleModel::where('business_role_id',
            $context->payload['business_role']['id'])->first()?->toArray();
        if(!$row) {
            $row = CustomBusinessRoleModel::create([
                'role' => $this->action,
                'business_role_id' =>  $context->payload['business_role']['id']
            ]);
        }
        $context->payload['roles'] = [
            ...$context->payload['roles'],
            $this->action
        ];
        return HookResult::pass([
            ...$context->payload
        ]);
    }
}

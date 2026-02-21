<?php

namespace Extensions\Smtp\Hooks;

use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Core\Permission\Infrastructure\Helpers\PermissionNode;
use Core\Permission\Infrastructure\Helpers\SupportUINav;
use Core\Permission\Infrastructure\Helpers\UINavGroup;

class AddNavMenu implements HookInterface
{
    function __construct(
        private PermissionNode $permissionNode,
        private SupportUINav $supportUINav
    ) {}
    public static function supports(HookContext $context): bool
    {
        return $context->action === HookAction::INDEX
            && $context->phase === HookPhase::RESPONSE
            && $context->module === 'Permission'
            && $context->timing === HookTiming::BEFORE;
    }

    public function handle(HookContext $context): HookResult
    {
        $this->permissionNode->setNode('smtp')
            ->setGroup("smtp.title")
            ->setPermission('index')
            ->setPermission('create')
            ->setPermission('test');
        $this->supportUINav->setData($context->payload['nav'])
            ->addItem(UINavGroup::SYSTEM, [
                'to'        => '/smtp',
                'link'      => null,
                'icon'      => "bi bi-person-workspace",
                'label'     => __("extension.smtp::messages.nav"),
                'ability'   => $this->permissionNode->getPermission("index"),
            ]);
        return HookResult::pass([
            ...$context->payload,
            'permissions' => [
                ...$context->payload['permissions'],
                ...$this->permissionNode->compile()
            ],
            'nav' => $this->supportUINav->compile()
        ]);
    }
}

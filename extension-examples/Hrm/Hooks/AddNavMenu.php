<?php

namespace Extensions\Hrm\Hooks;

use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;
use Core\Permission\Infrastructure\Helpers\PermissionNode;
use Core\Permission\Infrastructure\Helpers\SupportUINav;
use Core\Permission\Infrastructure\Helpers\UINavGroup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
        $this->permissionNode->setNode('hrm')
            ->setGroup("hrm.title")
            ->setPermission('nav')
            ->setPermission('approve-attendance')
            ->setPermission('index-attendance')
            ->setPermission('update-attendance')
            ->setPermission('create-attendance')
            ->setPermission('create-report')
            ->setPermission('index-report')
            ->setPermission('show-report')
            ->setPermission('create-monthly-summary')
            ->setPermission('index-monthly-summary')
            ->setPermission('show-monthly-summary')
            ->setPermission('index-leave')
            ->setPermission('create-leave')
            ->setPermission('approve-leave');

        $this->supportUINav->setData($context->payload['nav'])
            ->addItem(UINavGroup::ORGANIZATION, [
                'to'        => '/hrm',
                'link'      => null,
                'icon'      => "bi bi-person-workspace",
                'label'     => __("extension.hrm::messages.nav"),
                'ability'   => $this->permissionNode->getPermission("nav"),
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

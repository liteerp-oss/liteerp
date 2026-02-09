<?php 
namespace Core\BusinessRole\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\BusinessRole\Application\DTOs\ShowBusinessRoleRequest;
use Core\BusinessRole\Domain\Services\BusinessRoleService;
use Core\BusinessRole\Infrastructure\Helpers\SupportUINav;

class ViewBusinessRole {
    public function __construct(private BusinessRoleService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        $dto = ShowBusinessRoleRequest::fromArray($data);
        $role = $this->service->findOne([
            'business_id' => $dto->business_id,
            'role_user_id' => $dto->user_id
        ]);
        $roles = config('businessrole.roles.' . $role->role);
        $nav = config('businessrole.nav');
        $hook = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::UI,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    'roles' => $roles,
                    'nav' => $nav
                ],
                module: 'BusinessRole'
            )
        );
        $hook['nav'] = SupportUINav::build($hook['nav'],$hook['roles']);
        /**
         * Hook Timing ON really not necessary but to old Extensions continue working we need stay
         * And Maybe it will be remove on next version
         */
        $hook = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::UI,
                timing: HookTiming::ON,
                payload: [
                    ...$hook
                ],
                module: 'BusinessRole'
            )
        );
        $index = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::UI,
                timing: HookTiming::AFTER,
                payload: [
                    ...$hook
                ],
                module: 'BusinessRole'
            )
        );
        return [
            ...$index
        ];
    }
}
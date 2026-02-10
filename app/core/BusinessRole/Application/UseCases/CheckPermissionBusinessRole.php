<?php

namespace Core\BusinessRole\Application\UseCases;

use App\Exceptions\ForbiddenBiddenException;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\BusinessRole\Application\DTOs\CheckRoleBusinessRoleRequest;
use Core\BusinessRole\Domain\Services\BusinessRoleService;
use Illuminate\Support\Facades\Log;

class CheckPermissionBusinessRole
{
    public function __construct(private BusinessRoleService $service,private HookDispatcher $hooks) {}

    public function handle(CheckRoleBusinessRoleRequest $dto) : array
    {
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: $dto->toArray(),
                module: 'BusinessRole'
            )
        );
        $role = $this->service->findOne([
            'business_id' => $dto->business_id,
            'role_user_id' => $dto->user_id
        ]);
        $roles = config('businessrole.roles.' . $role->role);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    'business_role' => $role->toArray(),
                    'roles' => $roles,
                    ...$data
                ],
                module: 'BusinessRole'
            )
        );
        if(!in_array($dto->action,$data['roles'])) {
            throw new ForbiddenBiddenException(__("businessrole::messages.not_permission") . $dto->action);
        }
        return $data;
    }
}
<?php

namespace Core\PermissionGroup\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\PermissionGroup\Application\DTOs\CreatePermissionGroupAdminRequest;
use Core\PermissionGroup\Domain\Services\PermissionGroupService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreatePermissionGroupAdmin
{
    public function __construct(
        private PermissionGroupService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreatePermissionGroupAdminRequest::fromArray($data);

        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray(),
                ],
                module: 'PermissionGroup'
            )
        );
        $create = $this->service->createAdmin($data);

        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$create->toArray(),
                    'group_id' => $create->id,
                    'account_id' => $create->user_id
                ],
                module: 'PermissionGroup'
            )
        );

        Event::dispatch(Permission::PERMISSIONGROUP_CREATE_ADMIN->value, [...$data]);
        DB::commit();

        return $data;
    }
}

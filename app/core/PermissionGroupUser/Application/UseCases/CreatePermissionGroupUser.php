<?php

namespace Core\PermissionGroupUser\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\PermissionGroupUser\Application\DTOs\CreatePermissionGroupUserRequest;
use Core\PermissionGroupUser\Domain\Services\PermissionGroupUserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreatePermissionGroupUser
{
    public function __construct(
        private PermissionGroupUserService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreatePermissionGroupUserRequest::fromArray($data);

        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray(),
                ],
                module: 'PermissionGroupUser'
            )
        );
        $create = $this->service->create([
            ...$data
        ]);

        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$create->toArray()
                ],
                module: 'PermissionGroupUser'
            )
        );

        Event::dispatch(Permission::PERMISSIONGROUPUSER_CREATE->value, [
            ...$data
        ]);
        DB::commit();

        return $data;
    }
}

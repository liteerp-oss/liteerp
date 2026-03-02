<?php

namespace Core\PermissionGroupUser\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\PermissionGroupUser\Application\DTOs\DeletePermissionGroupUserRequest;
use Core\PermissionGroupUser\Domain\Services\PermissionGroupUserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DeletePermissionGroupUser
{
    public function __construct(
        private PermissionGroupUserService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeletePermissionGroupUserRequest::fromArray($data);

        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'PermissionGroupUser'
            )
        );

        $delete = $this->service->delete([
            ...$data
        ]);

        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$delete->toArray()
                ],
                module: 'PermissionGroupUser'
            )
        );

        Event::dispatch(Permission::PERMISSIONGROUPUSER_DELETE->value, [...$data]);
        DB::commit();

        return $data;
    }
}

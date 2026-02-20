<?php

namespace Core\PermissionGroup\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\PermissionGroup\Application\DTOs\CreatePermissionGroupRequest;
use Core\PermissionGroup\Domain\Services\PermissionGroupService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdatePermissionGroup
{
    public function __construct(
        private PermissionGroupService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreatePermissionGroupRequest::fromArray($data);

        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray(),
                ],
                module: 'PermissionGroup'
            )
        );

        $update = $this->service->update($data);

        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$update->toArray(),
                ],
                module: 'PermissionGroup'
            )
        );

        Event::dispatch('erp.permissiongroup.update', [...$data]);
        DB::commit();

        return $data;
    }
}

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

class CreatePermissionGroup
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

        $create = $this->service->create($data);

        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$create->toArray(),
                ],
                module: 'PermissionGroup'
            )
        );

        Event::dispatch('erp.permissiongroup.create', [...$data]);
        DB::commit();

        return $data;
    }
}

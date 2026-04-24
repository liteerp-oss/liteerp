<?php

namespace Core\Permission\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Permission\Application\DTOs\CreatePermissionRequest;
use Core\Permission\Domain\Services\PermissionService;
use Core\Permission\Infrastructure\Helpers\PermissionBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreatePermission
{
    public function __construct(
        private PermissionService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreatePermissionRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray(),
                ],
                module: 'Permission'
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
                    ...$create,
                ],
                module: 'Permission'
            )
        );

        Event::dispatch(Permission::PERMISSION_CREATE->value, $data);
        DB::commit();

        return $data;
    }
}

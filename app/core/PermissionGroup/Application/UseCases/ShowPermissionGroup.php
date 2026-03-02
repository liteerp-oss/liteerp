<?php

namespace Core\PermissionGroup\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\PermissionGroup\Application\DTOs\ShowPermissionGroupRequest;
use Core\PermissionGroup\Domain\Services\PermissionGroupService;
use Illuminate\Support\Facades\Event;

class ShowPermissionGroup
{
    public function __construct(
        private PermissionGroupService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        $dto = ShowPermissionGroupRequest::fromArray($data);
        $show = $this->service->show($dto->toArray());

        $payload = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::ON,
                payload: [
                    ...$data,
                    ...$show->toArray(),
                ],
                module: 'PermissionGroup'
            )
        );

        Event::dispatch(Permission::PERMISSIONGROUP_SHOW->value, [...$payload]);

        return $payload;
    }
}

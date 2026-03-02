<?php

namespace Core\PermissionGroupUser\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\PermissionGroupUser\Application\DTOs\ShowPermissionGroupUserRequest;
use Core\PermissionGroupUser\Domain\Services\PermissionGroupUserService;
use Illuminate\Support\Facades\Event;

class ShowPermissionGroupUser
{
    public function __construct(
        private PermissionGroupUserService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        $dto = ShowPermissionGroupUserRequest::fromArray($data);
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
                module: 'PermissionGroupUser'
            )
        );

        Event::dispatch(Permission::PERMISSIONGROUPUSER_SHOW->value, [...$payload]);

        return $payload;
    }
}

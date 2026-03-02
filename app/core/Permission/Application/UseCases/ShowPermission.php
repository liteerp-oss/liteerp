<?php

namespace Core\Permission\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Permission\Application\DTOs\ShowPermissionRequest;
use Core\Permission\Domain\Services\PermissionService;
use Illuminate\Support\Facades\Event;

class ShowPermission
{
    public function __construct(
        private PermissionService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        
        $dto = ShowPermissionRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray(),
                ],
                module: 'Permission'
            )
        );
        $show = $this->service->show($dto->toArray());

        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    'permissions' => $show,
                ],
                module: 'Permission'
            )
        );
        Event::dispatch(Permission::PERMISSION_SHOW->value, [...$data]);

        return $data;
    }
}

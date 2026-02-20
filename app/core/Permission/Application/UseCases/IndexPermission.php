<?php

namespace Core\Permission\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Permission\Application\DTOs\IndexPermissionRequest;
use Core\Permission\Domain\Services\PermissionService;
use Illuminate\Support\Facades\Event;
use Core\Permission\Infrastructure\Helpers\SupportUINav;
class IndexPermission
{
    public function __construct(
        private PermissionService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        
        $dto = IndexPermissionRequest::fromArray($data);
        $nav = config('permission.nav');
        $permissions = config('permission.permissions');
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray(),
                    'nav' => $nav,
                    'permissions' => $permissions
                ],
                module: 'Permission'
            )
        );
        $index = $this->service->index($dto->toArray());
        $data['nav'] = SupportUINav::build($data['nav'],$index);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    'roles' => $index,
                    'nav' => $data['nav']
                ],
                module: 'Permission'
            )
        );

        Event::dispatch('erp.permission.index', [...$data]);

        return $data;
    }
}

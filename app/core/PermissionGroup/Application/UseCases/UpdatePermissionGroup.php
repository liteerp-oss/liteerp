<?php

namespace Core\PermissionGroup\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

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

        Event::dispatch(Permission::PERMISSIONGROUP_UPDATE->value, [...$data]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.update', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'permissiongroup',
            'id' => $update->id,
            'data' => $update->toArray()        
        ]);
        DB::commit();

        return $data;
    }
}

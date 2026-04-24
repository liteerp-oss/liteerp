<?php

namespace Core\Warehouse\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Warehouse\Application\DTOs\CreateWarehouseRequest;
use Core\Warehouse\Domain\Services\WarehouseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateWarehouse
{
    public function __construct(
        private WarehouseService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateWarehouseRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Warehouse'
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
                    ...$create->toArray()
                ],
                module: 'Warehouse'
            )
        );
        Event::dispatch(Permission::WAREHOUSE_CREATE->value, [
            ...$data
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.create', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'warehouse',
            'id' => $create->id,
            'data' => $create->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}

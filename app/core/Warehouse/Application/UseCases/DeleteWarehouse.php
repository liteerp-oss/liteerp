<?php

namespace Core\Warehouse\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Warehouse\Application\DTOs\DeleteWarehouseRequest;
use Core\Warehouse\Domain\Services\WarehouseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DeleteWarehouse
{
    public function __construct(
        private WarehouseService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeleteWarehouseRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Warehouse'
            )
        );
        $delete = $this->service->delete($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$delete->toArray()
                ],
                module: 'Warehouse'
            )
        );
        Event::dispatch(Permission::WAREHOUSE_DELETE->value, [
            ...$data
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.delete', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'warehouse',
            'id' => $delete->id,
            'data' => $delete->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}

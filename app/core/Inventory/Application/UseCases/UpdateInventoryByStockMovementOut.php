<?php

namespace Core\Inventory\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Inventory\Application\DTOs\CreateInventoryRequest;
use Core\Inventory\Application\DTOs\UpdateInventoryByStockMovementOutRequest;
use Core\Inventory\Domain\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateInventoryByStockMovementOut
{
    public function __construct(
        private InventoryService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(UpdateInventoryByStockMovementOutRequest $dto)
    {
        DB::beginTransaction();
        foreach ($dto->list as $key => $value) {
            $updateData = new CreateInventoryRequest(
                product_id: $value['product_id'],
                warehouse_id: $value['warehouse_id'],
                quantity: -abs($value['quantity']),
                reserved_qty: -abs($value['reserved_qty']),
                created_by: $dto->created_by,
                business_id: $dto->business_id
            );
            $data = $this->hooks->dispatch(
                new HookContext(
                    action: HookAction::UPDATE,
                    phase: HookPhase::RESPONSE,
                    timing: HookTiming::BEFORE,
                    payload: [
                        ...$value,
                        ...$updateData->toArray()
                    ],
                    module: 'Inventory'
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
                        ...$update->toArray()
                    ],
                    module: 'Inventory'
                )
            );
            Event::dispatch(Permission::INVENTORY_UPDATE->value,[
                ...$data
            ]);
        }
        DB::commit();
        return;
    }
}

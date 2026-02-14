<?php

namespace Core\Inventory\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Inventory\Application\DTOs\CreateInventoryRequest;
use Core\Inventory\Application\DTOs\GetInventoryByProductWarehouseRequest;
use Core\Inventory\Application\DTOs\UpdateInventoryByStockMovementInRequest;
use Core\Inventory\Domain\Services\InventoryService;

class UpdateInventoryByStockMovementIn
{
    public function __construct(
        private InventoryService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(UpdateInventoryByStockMovementInRequest $dto)
    {
        foreach ($dto->list as $key => $value) {
            $find = new GetInventoryByProductWarehouseRequest(
                product_id: $value['product_id'],
                warehouse_id: $value['warehouse_id'],
                business_id: $dto->business_id
            );
            $row = $this->service->getByOneByProductAndWarehouse($find->toArray());

            $adapter = CreateInventoryRequest::fromArray([
                    'product_id' => $value['product_id'],
                    'warehouse_id' => $value['warehouse_id'],
                    'business_id' => $dto->business_id,
                    'quantity' => $value['qty_change'],
                    'user_id' => $dto->created_by
                ]);
            if (!$row) {
                $data = $this->hooks->dispatch(
                    new HookContext(
                        action: HookAction::CREATE,
                        phase: HookPhase::RESPONSE,
                        timing: HookTiming::BEFORE,
                        payload: $adapter->toArray(),
                        module: 'Inventory'
                    )
                );
                $create = $this->service->create($data);
                $this->hooks->dispatch(
                    new HookContext(
                        action: HookAction::CREATE,
                        phase: HookPhase::RESPONSE,
                        timing: HookTiming::AFTER,
                        payload: [
                            ...$data,
                            ...$create->toArray()
                        ],
                        module: 'Inventory'
                    )
                );
            } else {
                $data = $this->hooks->dispatch(
                    new HookContext(
                        action: HookAction::UPDATE,
                        phase: HookPhase::RESPONSE,
                        timing: HookTiming::BEFORE,
                        payload: $adapter->toArray(),
                        module: 'Inventory'
                    )
                );
                $update = $this->service->update($data);
                $this->hooks->dispatch(
                    new HookContext(
                        action: HookAction::UPDATE,
                        phase: HookPhase::RESPONSE,
                        timing: HookTiming::BEFORE,
                        payload: [
                            ...$data,
                            ...$update->toArray()
                        ],
                        module: 'Inventory'
                    )
                );
            }
        }
        return;
    }
}

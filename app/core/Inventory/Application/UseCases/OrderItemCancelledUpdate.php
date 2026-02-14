<?php

namespace Core\Inventory\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Inventory\Application\DTOs\OrderItemCancelledUpdateRequest;
use Core\Inventory\Application\DTOs\UpdateInventoryByIdRequest;
use Core\Inventory\Domain\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class OrderItemCancelledUpdate
{
    public function __construct(
        private InventoryService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(OrderItemCancelledUpdateRequest $dto)
    {
        DB::beginTransaction();

        foreach($dto->list as $key => $value) {
            $quantity = (float) ($value['buy_quantity']
            + $value['gift_quantity']
            + $value['compensation_quantity']
            + $value['conversion_quantity']);
            $adapter = UpdateInventoryByIdRequest::fromArray([
                'quantity'     => 0,
                'reserved_qty' => -abs($quantity),
                'created_by'   => $dto->created_by,
                'business_id'  => $dto->business_id,
                'id'    => $value['inventory_id'],
                'user_id' => $dto->created_by
            ]);
            $data = $this->hooks->dispatch(
                new HookContext(
                    action: HookAction::UPDATE,
                    phase: HookPhase::RESPONSE,
                    timing: HookTiming::BEFORE,
                    payload: $adapter->toArray(),
                    module: 'Inventory'
                )
            );
            $update = $this->service->updateById($data);
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
            Event::dispatch('erp.inventory.update', [
                'user_id' => $dto->created_by,
                'business_id' => $dto->business_id,
                ...$data
            ]);
        }
        DB::commit();
        return;
    }
}

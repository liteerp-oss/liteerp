<?php

namespace Core\Inventory\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Inventory\Application\DTOs\AdjustmentUpdateInventoryRequest;
use Core\Inventory\Domain\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class AdjustmentUpdateInventory
{
    public function __construct(
        private InventoryService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(AdjustmentUpdateInventoryRequest $dto)
    {
        DB::beginTransaction();
        $row = $this->service->getByOneByProductAndWarehouse($dto->toArray());
        if($row) {
            $data = $this->hooks->dispatch(
                new HookContext(
                    action: HookAction::UPDATE,
                    phase: HookPhase::RESPONSE,
                    timing: HookTiming::BEFORE,
                    payload: $dto->toArray(),
                    module: 'Inventory'
                )
            );
            $update = $this->service->update($data);
            $data = $this->hooks->dispatch(
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
            Event::dispatch('erp.inventory.update', [
                'user_id' => $dto->created_by,
                'business_id' => $dto->business_id,
                ...$data
            ]);
        } else {
            $data = $this->hooks->dispatch(
                new HookContext(
                    action: HookAction::CREATE,
                    phase: HookPhase::RESPONSE,
                    timing: HookTiming::BEFORE,
                    payload: $dto->toArray(),
                    module: 'Inventory'
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
                    module: 'Inventory'
                )
            );
            Event::dispatch('erp.inventory.create', [
                'user_id' => $dto->created_by,
                'business_id' => $dto->business_id,
                ...$data
            ]);
        }
        DB::commit();
    }
}

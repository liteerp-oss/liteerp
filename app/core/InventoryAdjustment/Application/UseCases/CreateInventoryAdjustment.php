<?php

namespace Core\InventoryAdjustment\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\InventoryAdjustment\Application\DTOs\CreateInventoryAdjustmentRequest;
use Core\InventoryAdjustment\Domain\Services\InventoryAdjustmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateInventoryAdjustment
{
    public function __construct(private InventoryAdjustmentService $service, private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: $data,
                module: 'InventoryAdjustment'
            )
        );
        $dto = CreateInventoryAdjustmentRequest::fromArray($data);
        $create = $this->service->create($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$create->toArray()
                ],
                module: 'InventoryAdjustment'
            )
        );
        Event::dispatch("erp.inventoryadjustment.create", [
            ...$data,
            ...$create->toArray(),
        ]);
        DB::commit();
        return $data;
    }
}
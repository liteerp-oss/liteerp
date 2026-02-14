<?php

namespace Core\Inventory\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Hooks\HookDispatcher;
use Core\Inventory\Application\DTOs\CreateInventoryRequest;
use Core\Inventory\Domain\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateInventory
{
    public function __construct(private InventoryService $service,
    private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: $data,
                module: 'Inventory'
            )
        );
        $dto = CreateInventoryRequest::fromArray($data);
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
        Event::dispatch('erp.inventory.create',[
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            ...$data
        ]);
        DB::commit();
        return $data;
    }
}
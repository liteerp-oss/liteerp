<?php

namespace Core\StockOut\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\StockOut\Application\DTOs\CreateStockOutRequest;
use Core\StockOut\Domain\Services\StockOutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateStockOut
{
    public function __construct(
        private StockOutService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data): array
    {
        DB::beginTransaction();
        $dto = CreateStockOutRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'StockOut'
            )
        );
        $update = $this->service->update($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$update->toArray()
                ],
                module: 'StockOut'
            )
        );
        $statusNotify = 'updated';
        if($update->isCompleted()) {
            Event::dispatch("erp.stockout.completed", [
                ...$data
            ]);
            $statusNotify = 'completed';
        } else if($update->isShipped()) {
            Event::dispatch("erp.stockout.shipped", [
                ...$data,
                'user_id' => $dto->created_by,
                'business_id' => $dto->business_id,
                'order_id' => $dto->order_id,
                'stock_out_id' => $update->id
            ]);
            $statusNotify = 'shipped';
        } else {
            Event::dispatch("erp.stockout.update", [
                ...$data,
                'user_id' => $dto->created_by,
                'business_id' => $dto->business_id,
                'order_id' => $dto->order_id
            ]);    
        }
        Event::dispatch("erp.notification.many", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $statusNotify,
            'entity_type' => 'stockout',
            'entity_id' => $update->id,
            'chanels' => ['db']
        ]);
        Event::dispatch("erp.notification.create", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $statusNotify,
            'entity_type' => 'stockout',
            'entity_id' => $update->id,
            'chanels' => ['db']
        ]);
        DB::commit();
        return $data;
    }
}

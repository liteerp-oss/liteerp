<?php

namespace Core\Order\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Order\Application\DTOs\UpdateOrderRequest;
use Core\Order\Domain\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateOrder
{
    public function __construct(
        private OrderService $service, 
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = UpdateOrderRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Order'
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
                module: 'Order'
            )
        );
        $notificationStatus = 'update';
        logs()->info("Order updated", $data);
        if($update->isApproved()) {
            Event::dispatch("erp.order.approved", [
                ...$data
            ]);  
            $notificationStatus = "approved";
        } else if($update->isCancelled()) {
            Event::dispatch("erp.order.cancelled", [
                ...$data
            ]);
            $notificationStatus = "cancelled";
        } else {
            Event::dispatch("erp.order.update", [
                ...$data
            ]);
        }
        Event::dispatch("erp.notification.many", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $notificationStatus,
            'entity_type' => 'order',
            'entity_id' => $update->id,
            'chanels' => ['db'],
            'roles' => ['admin','manager']
        ]);
        Event::dispatch("erp.notification.create", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $notificationStatus,
            'entity_type' => 'order',
            'entity_id' => $update->id,
            'chanels' => ['db']
        ]);
        DB::commit();
        return $data;
    }
}

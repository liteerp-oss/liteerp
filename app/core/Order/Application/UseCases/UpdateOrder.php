<?php

namespace Core\Order\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
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
        if($update->isApproved()) {
            Event::dispatch(Permission::ORDER_APPROVED->value, [
                ...$data
            ]);  
        } else if($update->isCancelled()) {
            Event::dispatch(Permission::ORDER_CANCELLED->value, [
                ...$data
            ]); 
        } else {
            Event::dispatch(Permission::ORDER_UPDATE->value, [
                ...$data
            ]);
        }
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $update->getStatus(),
            'entity_type' => 'order',
            'entity_id' => $update->id,
            'chanels' => ['db'],
            'title' => "order::messages.title",
            'message' => "order::messages.notification.{$update->getStatus()}",
            'message_params' => [
                'username' => $data['username']
            ],
            'link' => "/orders?form=edit&id=$update->id"
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::ORDER_CREATE->value,
                Permission::ORDER_APPROVED->value,
                Permission::ORDER_CANCELLED->value,
                Permission::ORDER_DELETE->value,
                Permission::ORDER_UPDATE->value
            ]
        ]);
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.update', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'order',
            'id' => $update->id,
            'data' => $update->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}

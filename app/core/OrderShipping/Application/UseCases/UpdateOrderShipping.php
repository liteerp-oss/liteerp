<?php

namespace Core\OrderShipping\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\OrderShipping\Application\DTOs\CreateOrderShippingRequest;
use Core\OrderShipping\Domain\Services\OrderShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateOrderShipping
{
    public function __construct(private OrderShippingService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {

        DB::beginTransaction();
        $dto = CreateOrderShippingRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'OrderShipping'
            )
        );
        $oldData = $this->service->findByOrderId($data);
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
                module: 'OrderShipping'
            )
        );
        Event::dispatch(Permission::ORDERSHIPPING_UPDATE->value,[
            ...$data,
            'shipping_fee_estimated' => $oldData->shipping_fee_estimated,
            'old_shipping_fee_actual' => $oldData->shipping_fee_actual
        ]);
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'updated',
            'entity_type' => 'order',
            'entity_id' => $update->id,
            'chanels' => ['db'],
            'message' => "ordershipping::messages.notification.updated",
            'message_params' => [
                'username' => $data['username']
            ]
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::STOCKOUT_SHIPPED->value,
                Permission::STOCKOUT_COMPLETED->value,
                Permission::STOCKOUT_UPDATE->value,
                Permission::STOCKOUT_CREATE->value,
                Permission::ORDER_APPROVED->value,
                Permission::ORDER_CANCELLED->value,
                Permission::ORDER_CREATE->value,
                Permission::ORDER_UPDATE->value,
                Permission::INVOICEOUT_APPROVED->value,
                Permission::INVOICEOUT_UNAPPROVED->value,
                Permission::INVOICEOUT_UPDATE->value
            ]
        ]);
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.update', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'ordershipping',
            'id' => $update->id,
            'data' => $update->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}
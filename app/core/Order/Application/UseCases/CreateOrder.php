<?php

namespace Core\Order\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\Order\Application\DTOs\CreateOrderRequest;
use Core\Order\Domain\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateOrder
{
    public function __construct(private OrderService $service, 
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateOrderRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Order'
            )
        );
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
                module: 'Order'
            )
        );
        Event::dispatch(Permission::ORDER_CREATE->value, [
            ...$data
        ]);
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'created',
            'entity_type' => 'order',
            'entity_id' => $create->id,
            'chanels' => ['db'],
            'title' => "order::messages.title",
            'message' => "order::messages.notification.created",
            'message_params' => [
                'username' => $data['username']
            ],
            'link' => "/orders?form=edit&id=$create->id"
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::ORDER_CREATE->value
            ]
        ]);
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);

        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.create', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'order',
            'id' => $create->id,
            'data' => $create->toArray()        
        ]);

        DB::commit();
        return $data;
    }
}
<?php

namespace Core\Purchase\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\Purchase\Application\DTOs\CreatePurchaseRequest;
use Core\Purchase\Domain\Services\PurchaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreatePurchase
{
    public function __construct(
        private PurchaseService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data): array
    {
        DB::beginTransaction();
        $dto = CreatePurchaseRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Purchase'
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
                module: 'Purchase'
            )
        );
        Event::dispatch(Permission::PURCHASE_CREATE->value, [
            ...$data
        ]);
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'created',
            'entity_type' => 'purchase',
            'entity_id' => $create->id,
            'chanels' => ['db'],
            'title' => "purchase::messages.title",
            'message' => 'purchase::messages.notification.created',
            'message_params' => [
                'username' => $data['username']
            ]
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::PURCHASE_APPROVED,
                Permission::PURCHASE_CANCELLED
            ]
        ]);
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.create', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'purchase',
            'id' => $create->id,
            'data' => $create->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}

<?php

namespace Core\StockOut\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
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
        if($update->isCompleted()) {
            Event::dispatch(Permission::STOCKOUT_COMPLETED->value, [
                ...$data,
                'stock_out_id' => $update->id
            ]);
        } else if($update->isShipped()) {
            Event::dispatch(Permission::STOCKOUT_SHIPPED->value, [
                ...$data,
                'stock_out_id' => $update->id
            ]);
        } else {
            Event::dispatch(Permission::STOCKOUT_UPDATE->value, [
                ...$data,
                'stock_out_id' => $update->id
            ]);    
        }
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $update->getStatus(),
            'entity_type' => 'stockout',
            'entity_id' => $update->id,
            'chanels' => ['db'],
            'title' => 'stockout::messages.title',
            'message' => "stockout::messages.notification.{$update->getStatus()}",
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
            'type' => 'stockout',
            'id' => $update->id,
            'data' => $update->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}

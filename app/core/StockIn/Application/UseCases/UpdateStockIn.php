<?php

namespace Core\StockIn\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\StockIn\Application\DTOs\CreateStockInRequest;
use Core\StockIn\Domain\Services\StockInService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateStockIn
{
    public function __construct(
        private StockInService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateStockInRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'StockIn'
            )
        );
        $update = $this->service->update($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$update->toArray()
                ],
                module: 'StockIn'
            )
        );
        $status = $update->isReceived() ? 'received' : 'updated';
        if($update->isReceived()) {
            Event::dispatch(Permission::STOCKIN_RECEIVED->value, [
                ...$data
            ]);    
        } else {
            Event::dispatch(Permission::STOCKIN_UPDATE->value, [
                ...$data
            ]);   
        }
        
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $status,
            'entity_type' => 'stockin',
            'entity_id' => $update->id,
            'chanels' => ['db'],
            'title' => "stockin::messages.title",
            'message' => "stockin::messages.notification.$status",
            'message_params' => [
                'username' => $data['username']
            ]
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::PURCHASE_APPROVED,
                Permission::PURCHASE_CANCELLED,
                Permission::STOCKIN_CANCELLED,
                Permission::STOCKIN_RECEIVED,
                Permission::STOCKIN_UPDATE,
                Permission::INVOICEIN_APPROVED
            ]
        ]);
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.update', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'stockin',
            'id' => $update->id,
            'data' => $update->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}

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
use Illuminate\Support\Facades\Log;

class CreateStockIn
{
    public function __construct(private StockInService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateStockInRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'StockIn'
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
                module: 'StockIn'
            )
        );
        Event::dispatch(Permission::STOCKIN_CREATE->value, [
            ...$data,
        ]);
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'created',
            'entity_type' => 'stockin',
            'entity_id' => $create->id,
            'chanels' => ['db'],
            'message' => "stockin::messages.notification.created",
            'title' => "stockin::messages.title",
            'message_params' => [
                'username' => $data['username']
            ]
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::STOCKIN_UPDATE,
                Permission::STOCKIN_RECEIVED,
                Permission::STOCKIN_CANCELLED
            ]
        ]);
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.create', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'stockin',
            'id' => $create->id,
            'data' => $create->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}
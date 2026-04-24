<?php

namespace Core\StockOut\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\StockOut\Application\DTOs\CreateStockOutRequest;
use Core\StockOut\Domain\Entities\StockOut;
use Core\StockOut\Domain\Services\StockOutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
class CreateStockOut
{
    public function __construct(private StockOutService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data) : array
    {
        DB::beginTransaction();
        $dto = CreateStockOutRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'StockOut'
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
                module: 'StockOut'
            )
        );
        Event::dispatch(Permission::STOCKOUT_CREATE->value, [
            ...$data
        ]);
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'created',
            'entity_type' => 'stockout',
            'entity_id' => $create->id,
            'chanels' => ['db'],
            'title' => 'stockout::messages.title',
            'message' => "stockout::messages.notification.created",
            'message_params' => [
                'username' => $data['username']
            ]
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::STOCKOUT_COMPLETED->value,
                Permission::STOCKOUT_SHIPPED->value,
                Permission::STOCKOUT_UPDATE->value,
                Permission::STOCKOUT_DELETE->value,
                Permission::STOCKOUT_SHOW->value
            ]
        ]);
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.create', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'stockout',
            'id' => $create->id,
            'data' => $create->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}
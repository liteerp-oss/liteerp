<?php

namespace Core\OrderShipping\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\OrderShipping\Application\DTOs\CreateOrderShippingRequest;
use Core\OrderShipping\Domain\Services\OrderShippingService;
use Illuminate\Support\Facades\Event;

class CreateOrderShipping
{
    public function __construct(private OrderShippingService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        $dto = CreateOrderShippingRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'OrderShipping'
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
                module: 'OrderShipping'
            )
        );
        Event::dispatch(Permission::ORDERSHIPPING_CREATE->value,[
            ...$data
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.create', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'ordershipping',
            'id' => $create->id,
            'data' => $create->toArray()        
        ]);
        return $data;
    }
}
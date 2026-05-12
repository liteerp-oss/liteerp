<?php

namespace Core\Order\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\Order\Application\DTOs\CreateOrderRequest;
use Core\Order\Application\DTOs\ShowOrderRequest;
use Core\Order\Domain\Services\OrderService;
use Illuminate\Support\Facades\Event;

class ShowOrder
{
    public function __construct(private OrderService $service, 
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        $dto = ShowOrderRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Order'
            )
        );
        
        $show = $this->service->show($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$show
                ],
                module: 'Order'
            )
        );
        // Event::dispatch(Permission::ORDER_SHOW->value, [
        //     ...$data
        // ]);
        return $data;
    }
}
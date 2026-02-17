<?php

namespace Core\OrderShipping\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\OrderShipping\Application\DTOs\ShowOrderShippingRequest;
use Core\OrderShipping\Domain\Services\OrderShippingService;

class ShowOrderShipping
{
    public function __construct(private OrderShippingService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        $dto = ShowOrderShippingRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'OrderShipping'
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
                module: 'OrderShipping'
            )
        );
        return $data;
    }
}
<?php

namespace Core\Shipping\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use Core\Shipping\Application\DTOs\CreateShippingRequest;
use Core\Shipping\Application\DTOs\ShowShippingRequest;
use Core\Shipping\Domain\Services\ShippingService;
use Illuminate\Support\Facades\Event;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
class ShowShipping
{
    public function __construct(private ShippingService $service,
    private HookDispatcher $dispatch) {}

    public function handle(array $data)
    {
        $dto = ShowShippingRequest::fromArray($data);
        $data = $this->dispatch->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Shipping'
            )
        );
        $show = $this->service->show($dto->toArray());
        $data = $this->dispatch->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$show->toArray()
                ],
                module: 'Shipping'
            )
        );
        // Event::dispatch(Permission::SHIPPING_SHOW->value, [
        //     ...$data,
        // ]);
        return $data;
    }
}
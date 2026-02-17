<?php

namespace Core\Shipping\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Shipping\Application\DTOs\CreateShippingRequest;
use Core\Shipping\Domain\Services\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateShipping
{
    public function __construct(private ShippingService $service,
    private HookDispatcher $dispatch) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateShippingRequest::fromArray($data);
        $data = $this->dispatch->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Shipping'
            )
        );
        $create = $this->service->create($dto->toArray());
        $data = $this->dispatch->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$create->toArray()
                ],
                module: 'Shipping'
            )
        );
        Event::dispatch("erp.shipping.create", [
            ...$data
        ]);
        DB::commit();
        return $data;
    }
}

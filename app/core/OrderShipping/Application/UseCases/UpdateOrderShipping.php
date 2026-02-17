<?php

namespace Core\OrderShipping\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\OrderShipping\Application\DTOs\CreateOrderShippingRequest;
use Core\OrderShipping\Domain\Services\OrderShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateOrderShipping
{
    public function __construct(private OrderShippingService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {

        DB::beginTransaction();
        $dto = CreateOrderShippingRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'OrderShipping'
            )
        );
        $oldData = $this->service->findById($data);
        $update = $this->service->update($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$update->toArray()
                ],
                module: 'OrderShipping'
            )
        );
        Event::dispatch('erp.ordershipping.update',[
            ...$data,
            'shipping_fee_estimated' => $oldData->shipping_fee_estimated,
            'old_shipping_fee_actual' => $oldData->shipping_fee_actual
        ]);
        DB::commit();
        return $data;
    }
}
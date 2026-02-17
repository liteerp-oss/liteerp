<?php

namespace Core\OrderItem\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\OrderItem\Application\DTOs\CreateOrderItemRequest;
use Core\OrderItem\Domain\Services\OrderItemService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateOrderItem
{
    public function __construct(private OrderItemService $service, private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateOrderItemRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'OrderItem'
            )
        );

        $item = $this->service->update($dto->toArray());

        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$item->toArray()
                ],
                module: 'OrderItem'
            )
        );

        Event::dispatch('erp.orderitem.update',[
            ...$data,
            'qty_change' => (float) ($item->buy_quantity
                + $item->gift_quantity
                + $item->compensation_quantity
                + $item->conversion_quantity),
        ]);

        DB::commit();

        return $data;
    }
}
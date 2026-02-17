<?php

namespace Core\OrderItem\Application\UseCases;

use App\Supports\Hooks\HookDispatcher;
use Core\OrderItem\Domain\Services\OrderItemService;
use Core\OrderItem\Application\DTOs\CompletedOrderItemRequest;
use Illuminate\Support\Facades\Event;

class CompletedOrderItem
{
    public function __construct(private OrderItemService $service, private HookDispatcher $hooks) {}

    public function handle(array $data)
    {

        $dto = CompletedOrderItemRequest::fromArray($data);
        $list = $this->service->indexForStockMovementOut($dto->toArray());

        Event::dispatch('erp.orderitem.completed',[
            ...$data,
            ...$dto->toArray(),
            'list' => $list,
        ]);
    }
}
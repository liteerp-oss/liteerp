<?php

namespace Core\OrderItem\Application\UseCases;


use Core\OrderItem\Application\DTOs\CancelledOrderItemRequest;
use Core\OrderItem\Domain\Services\OrderItemService;
use Illuminate\Support\Facades\Event;

class CancelledOrderItem
{
    public function __construct(private OrderItemService $service) {}

    public function handle(array $data)
    {
        $dto = CancelledOrderItemRequest::fromArray($data);
        $list = $this->service->indexForStockMovementOut($dto->toArray());
        if(count($list) >= 1) {

            Event::dispatch('erp.orderitem.cancelled',[
                ...$data,
                'business_id' => $dto->business_id,
                'user_id'   => $dto->created_by,
                'order_id'  => $dto->order_id,
                'list' => $list
            ]);
        }
    }
}
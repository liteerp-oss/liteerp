<?php

namespace Core\OrderItem\Application\UseCases;

use App\Exceptions\BadException;
use Core\OrderItem\Domain\Services\OrderItemService;
use Core\Order\Application\UseCases\FindOneById;
use Core\OrderItem\Application\DTOs\CheckExistsOrderItemRequest;

class CheckExistsOrderItem
{
    public function __construct(private OrderItemService $service) {}

    public function handle(CheckExistsOrderItemRequest $dto)
    {
        $list = $this->service->indexForStockMovementOut($dto->toArray());
        if(empty($list) || count($list) === 0) {
            throw new BadException(__("orderitem::messages.no_products_added"));
        }
    }
}
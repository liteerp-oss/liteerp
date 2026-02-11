<?php

namespace Core\Order\Application\UseCases;

use App\Exceptions\BadException;
use Core\Order\Application\DTOs\CheckUpdateShippingOrderRequest;
use Core\Order\Domain\Services\OrderService;

class CheckUpdateShippingOrder
{
    public function __construct(private OrderService $service) {}

    public function handle(CheckUpdateShippingOrderRequest $dto)
    {
        $row = $this->service->findOneById($dto->toArray());
        if($row->isCancelled()) {
            throw new BadException(__("order::messages.order_cancelled"));
        }
        return $row;
    }
}
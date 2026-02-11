<?php

namespace Core\Order\Application\UseCases;

use App\Exceptions\BadException;
use Core\Order\Application\DTOs\CheckOrderCancelledRequest;
use Core\Order\Domain\Services\OrderService;

class CheckOrderCancelled
{
    public function __construct(private OrderService $service) {}

    public function handle(CheckOrderCancelledRequest $dto)
    {
        $row = $this->service->findOneById($dto->toArray());
        if($row->isCancelled()) {
            throw new BadException(__("order::messages.order_cancelled"));
        }
        return $row;
    }
}
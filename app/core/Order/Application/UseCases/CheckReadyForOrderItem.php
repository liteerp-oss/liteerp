<?php

namespace Core\Order\Application\UseCases;

use App\Exceptions\BadException;
use Core\Order\Application\DTOs\CheckReadyForOrderItemRequest;
use Core\Order\Domain\Services\OrderService;

class CheckReadyForOrderItem
{
    public function __construct(private OrderService $service) {}

    public function handle(CheckReadyForOrderItemRequest $dto)
    {
        $row = $this->service->findOneById($dto->toArray());
        if(!$row->isPending()) {
            throw new BadException(__("order::messages.status_cannot_modify_items"));
        }
        return $row;
    }
}
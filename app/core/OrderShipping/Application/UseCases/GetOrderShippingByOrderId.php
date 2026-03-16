<?php

namespace Core\OrderShipping\Application\UseCases;

use Core\OrderShipping\Application\DTOs\GetOrderShippingByOrderIdRequest;
use Core\OrderShipping\Domain\Services\OrderShippingService;

class GetOrderShippingByOrderId
{
    public function __construct(private OrderShippingService $service) {}

    public function handle(array $data)
    {
        $dto = GetOrderShippingByOrderIdRequest::fromArray($data);
        return $this->service->getByOrderId($dto->toArray());
    }
}
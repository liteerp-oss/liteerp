<?php

namespace Core\OrderShipping\Application\UseCases;

use App\Exceptions\BadException;
use Core\OrderShipping\Application\DTOs\CheckReadyOrderShippingRequest;
use Core\OrderShipping\Domain\Services\OrderShippingService;

class CheckReadyOrderShipping
{
    public function __construct(private OrderShippingService $service) {}

    public function handle(CheckReadyOrderShippingRequest $dto)
    {
        $OrderShipping = $this->service->findByOrderId($dto->toArray());
        if(!$OrderShipping->isReady()) {
            throw new BadException(__("ordershipping::messages.not_ready"));
        }
    }
}
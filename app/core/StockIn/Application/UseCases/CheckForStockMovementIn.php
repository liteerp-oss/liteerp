<?php

namespace Core\StockIn\Application\UseCases;

use App\Exceptions\BadException;
use Core\StockIn\Application\DTOs\CheckForStockMovementInRequest;
use Core\StockIn\Domain\Services\StockInService;

class CheckForStockMovementIn
{
    public function __construct(private StockInService $service) {}

    public function handle(CheckForStockMovementInRequest $dto)
    {
        $stock = $this->service->findById($dto->toArray());
        if($stock->isReceived()) {
            throw new BadException(__("stockin::messages.received_cannot_update"));
        }
        if($stock->isCancelled()) {
            throw new BadException(__("stockin::messages.cancelled_cannot_update"));
        }
        return $stock;
    }
}

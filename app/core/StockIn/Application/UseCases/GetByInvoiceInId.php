<?php

namespace Core\StockIn\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\StockIn\Application\DTOs\ShowStockInRequest;
use Core\StockIn\Domain\Services\StockInService;

class GetByInvoiceInId
{
    public function __construct(private StockInService $service) {}

    public function handle(array $data)
    {
        $stock = $this->service->getByInvoiceInId($data);
        return $stock;
    }
}
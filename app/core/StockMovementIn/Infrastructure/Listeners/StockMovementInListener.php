<?php

namespace Core\StockMovementIn\Infrastructure\Listeners;

use Core\StockMovementIn\Application\UseCases\CompleteStockMovementIn;
use Illuminate\Support\Facades\Event;

class StockMovementInListener
{
    public function __construct(private CompleteStockMovementIn $completeStockMovementIn)
    {
        
    }
    public function handle()
    {
        Event::listen(
            "erp.stockin.*",
            function (string $eventName, array $data) {
                if ($eventName === 'erp.stockin.received') {
                    $this->completeStockMovementIn->handle($data);
                }
            }
        );
    }
}

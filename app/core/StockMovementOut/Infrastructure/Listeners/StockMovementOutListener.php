<?php

namespace Core\StockMovementOut\Infrastructure\Listeners;

use Core\StockMovementOut\Application\DTOs\CreateManyStockMovementOutRequest;
use Core\StockMovementOut\Application\UseCases\CreateManyStockMovementOut;
use Illuminate\Support\Facades\Event;

class StockMovementOutListener
{
    function __construct(private CreateManyStockMovementOut $CreateManyStockMovementOut)
    {
        
    }
    public function handle() {
        Event::listen(
            'erp.orderitem.*',
            function (string $eventName, array $orderItems) {

                if ($eventName === 'erp.orderitem.completed') {
                    $this->CreateManyStockMovementOut->handle(
                        CreateManyStockMovementOutRequest::fromArray($orderItems)
                    );
                }
            }
        );
    }
}

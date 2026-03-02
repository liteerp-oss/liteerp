<?php

namespace Core\StockIn\Infrastructure\Listeners;

use Core\StockIn\Application\DTOs\CancelledStockInRequest;
use Core\StockIn\Application\DTOs\CheckForStockMovementInRequest;
use Core\StockIn\Application\UseCases\CancelledStockIn;
use Core\StockIn\Application\UseCases\CheckForStockMovementIn;
use Core\StockIn\Application\UseCases\CreateStockIn;
use Illuminate\Support\Facades\Event;

class StockInListener
{
    function __construct(
        private CreateStockIn $createStockIn,
        private CheckForStockMovementIn $checkForStockMovementIn,
        private CancelledStockIn $cancelledStockIn
    ) {}
    public function handle()
    {
        Event::listen('erp.invoicein.*', function (string $eventName, array $data) {
            if ($eventName === 'erp.invoicein.approved') {
                $this->createStockIn->handle($data);
            } else if ($eventName === 'erp.invoicein.cancelled') {
                $this->cancelledStockIn->handle($data);
            }
        });
        Event::listen(
            'erp.stockmovementin.*',
            function (string $eventName, array $data) {
                if (
                    $eventName === 'erp.stockmovementin.update'
                    || $eventName === 'erp.stockmovementin.create'
                ) {
                    $this->checkForStockMovementIn->handle(CheckForStockMovementInRequest::fromArray([
                        'id' => $data['stock_in_id'],
                        'business_id' => $data['business_id'],
                        'user_id' => $data['user_id']
                    ]));
                }
            }
        );
    }
}

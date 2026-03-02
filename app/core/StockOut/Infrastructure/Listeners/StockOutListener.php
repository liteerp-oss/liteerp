<?php

namespace Core\StockOut\Infrastructure\Listeners;
use Core\StockOut\Application\UseCases\CancelledStockOutByOrderCancelled;
use Core\StockOut\Application\UseCases\CreateStockOut;
use Illuminate\Support\Facades\Event;

class StockOutListener
{
    function __construct(private CreateStockOut $CreateStockOut,
        private CancelledStockOutByOrderCancelled $CancelledStockOutByOrderCancelled)
    {
        
    }
    public function handle(
        
    ) {
        Event::listen(
            'erp.invoiceout.*',
            function (string $eventName, array $data) {
                if ($eventName === 'erp.invoiceout.approved') {
                    $this->CreateStockOut->handle($data);
                } else if ($eventName === 'erp.invoiceout.unapproved') {
                    $this->CancelledStockOutByOrderCancelled->handle($data);
                }
            }
        );
    }
}

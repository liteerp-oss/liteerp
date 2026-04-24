<?php

namespace Core\InvoiceOut\Infrastructure\Listeners;

use Core\InvoiceOut\Application\DTOs\UnapproveInvoiceOutByOrderCancelledRequest;
use Core\InvoiceOut\Application\UseCases\CreateInvoiceOut;
use Core\InvoiceOut\Application\UseCases\UnapproveInvoiceOutByOrderCancelled;
use Core\InvoiceOut\Application\UseCases\UpdateTotalByShippingFee;
use Illuminate\Support\Facades\Event;

class InvoiceOutListener
{
    function __construct(private CreateInvoiceOut $createInvoiceOut,
    private UnapproveInvoiceOutByOrderCancelled $UnapproveInvoiceOutByOrderCancelled,
    private UpdateTotalByShippingFee $UpdateTotalByShippingFee)
    {
    }
    public function handle()
    {
        Event::listen(
            'erp.order.*',
            function (string $eventName, array $data) {
                if ($eventName === 'erp.order.cancelled') {
                    $this->UnapproveInvoiceOutByOrderCancelled->handle($data);
                }
            }
        );
        Event::listen(
            'erp.orderitem.*',
            function (string $eventName, array $data) {
                if ($eventName === 'erp.orderitem.summary') {
                    $this->createInvoiceOut->handle($data);
                } 
            }
        );
        /**
         * If stock out update  real shipping fee
         * Then Invoice out shuold update total price 
         */
        Event::listen(
            "erp.ordershipping.*",
            function (string $eventName, array $data) {
                if ($eventName === "erp.ordershipping.update") {
                    $this->UpdateTotalByShippingFee->handle($data);
                } 
            }
        );
    }
}
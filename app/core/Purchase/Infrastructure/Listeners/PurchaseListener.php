<?php

namespace Core\Purchase\Infrastructure\Listeners;

use Core\Purchase\Application\UseCases\CheckForPurchaseCancelled;
use Core\Purchase\Application\UseCases\CheckForPurchaseItem;
use Illuminate\Support\Facades\Event;

class PurchaseListener
{
    function __construct(private CheckForPurchaseItem $checkForPurchaseItem,
        private CheckForPurchaseCancelled $checkForPurchaseCancelled)
    {
        
    }
    public function handle(
        
    ) {
        Event::listen(
            'erp.purchaseitem.*',
            function (string $eventName, array $data) {
                if (
                    $eventName === 'erp.purchaseitem.create'
                    || $eventName === 'erp.purchaseitem.update'
                    || $eventName === 'erp.purchaseitem.delete'
                ) {
                    $this->checkForPurchaseItem->handle([
                        'business_id' => $data['business_id'],
                        'user_id' => $data['user_id'],
                        'id' => $data['purchase_id']
                    ]);
                }
            }
        );
        Event::listen(
            'erp.invoicein.*',
            function (string $eventName, array $data) {
                if (
                    $eventName === 'erp.invoicein.create'
                    || $eventName === 'erp.invoicein.update'
                ) {
                    $this->checkForPurchaseCancelled->handle([
                        'business_id' => $data['business_id'],
                        'user_id' => $data['user_id'],
                        'id' => $data['purchase_id']
                    ]);
                }
            }
        );
    }
}

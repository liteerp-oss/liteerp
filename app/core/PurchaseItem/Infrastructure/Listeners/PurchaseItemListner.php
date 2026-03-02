<?php

namespace Core\PurchaseItem\Infrastructure\Listeners;

use Core\PurchaseItem\Application\DTOs\CheckForPurchaseRequestedRequest;
use Core\PurchaseItem\Application\UseCases\CheckForPurchaseRequested;
use Core\PurchaseItem\Application\UseCases\CheckForStockMovementIn;
use Illuminate\Support\Facades\Event;

class PurchaseItemListner
{
    function __construct(private CheckForStockMovementIn $checkForStockMovementIn,
        private CheckForPurchaseRequested $CheckForPurchaseRequested)
    {
        
    }
    public function handle()
    {
        Event::listen(
            'erp.stockmovementin.*',
            function (string $eventName, array $data) {
                if (
                    $eventName === 'erp.stockmovementin.create'
                    || $eventName === 'erp.stockmovementin.update'
                ) {
                    $this->checkForStockMovementIn
                        ->handle([
                            ...$data,
                            'id' => $data['purchase_item_id'],
                            'qty_change' => $data['qty_change']
                        ]);
                }
            }
        );
        Event::listen(
            'erp.purchase.*',
            function (string $eventName, array $data) {
                if (
                    $eventName === 'erp.purchase.requested'
                ) {
                    $this->CheckForPurchaseRequested->handle([
                            ...$data,
                            'purchase_id' => $data['id']
                        ]);
                }
            }
        );
    }
}

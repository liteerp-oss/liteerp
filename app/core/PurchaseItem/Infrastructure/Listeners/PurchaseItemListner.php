<?php

namespace Core\PurchaseItem\Infrastructure\Listeners;

use Core\PurchaseItem\Application\DTOs\CheckForPurchaseRequestedRequest;
use Core\PurchaseItem\Application\DTOs\CheckForStockMovementInRequest;
use Core\PurchaseItem\Application\UseCases\CheckForPurchaseRequested;
use Core\PurchaseItem\Application\UseCases\CheckForStockMovementIn;
use Illuminate\Support\Facades\Event;

class PurchaseItemListner
{
    public function handle(CheckForStockMovementIn $checkForStockMovementIn,
        CheckForPurchaseRequested $CheckForPurchaseRequested)
    {
        Event::listen(
            'erp.stockmovementin.*',
            function (string $eventName, array $data) use ($checkForStockMovementIn) {
                if (
                    $eventName === 'erp.stockmovementin.create'
                    || $eventName === 'erp.stockmovementin.update'
                ) {
                    $checkForStockMovementIn
                        ->handle(CheckForStockMovementInRequest::fromArray([
                            'id' => $data['purchase_item_id'],
                            'business_id' => $data['business_id'],
                            'user_id' => $data['user_id'],
                            'qty_change' => $data['qty_change']
                        ]));
                }
            }
        );
        Event::listen(
            'erp.purchase.*',
            function (string $eventName, array $data) use ($CheckForPurchaseRequested) {
                if (
                    $eventName === 'erp.purchase.requested'
                ) {
                    $CheckForPurchaseRequested->handle(
                        CheckForPurchaseRequestedRequest::fromArray([
                            'business_id' => $data['business_id'],
                            'purchase_id' => $data['id'],
                            'user_id' => $data['user_id']
                        ])
                    );
                }
            }
        );
    }
}

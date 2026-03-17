<?php

namespace Core\StockMovementIn\Infrastructure\Listeners;

use App\Supports\Permissions\Enums\Permission;
use Core\StockMovementIn\Application\UseCases\CheckStockMovementInForInventoryAdjustment;
use Core\StockMovementIn\Application\UseCases\CheckStockMovementInForOrderItem;
use Core\StockMovementIn\Application\UseCases\CompleteStockMovementIn;
use Illuminate\Support\Facades\Event;

class StockMovementInListener
{
    public function __construct(private CompleteStockMovementIn $completeStockMovementIn,
        private CheckStockMovementInForOrderItem $CheckStockMovementInForOrderItem,
        private CheckStockMovementInForInventoryAdjustment $CheckStockMovementInForInventoryAdjustment)
    {
        
    }
    public function handle()
    {
        Event::listen(
            "erp.stockin.*",
            function (string $eventName, array $data) {
                if ($eventName === Permission::STOCKIN_RECEIVED->value) {
                    $this->completeStockMovementIn->handle($data);
                }
            }
        );
        Event::listen(
            "erp.orderitem.*",
            function (string $eventName, array $data) {
                if ($eventName === Permission::ORDERITEM_CREATE->value
                    || $eventName === Permission::ORDERITEM_UPDATE->value) {
                    $this->CheckStockMovementInForOrderItem->handle($data);
                }
            }
        );
        Event::listen(
            "erp.inventoryadjustment.*",
            function (string $eventName, array $data) {
                if ($eventName === Permission::INVENTORYADJUSTMENT_CREATE->value) {
                    $this->CheckStockMovementInForInventoryAdjustment->handle($data);
                }
            }
        );
    }
}

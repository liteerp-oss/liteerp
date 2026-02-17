<?php

namespace Core\Inventory\Infrastructure\Listeners;

use Core\Inventory\Application\DTOs\OrderItemCancelledUpdateRequest;
use Core\Inventory\Application\DTOs\OrderItemCompletedUpdateRequest;
use Core\Inventory\Application\DTOs\UpdateInventoryByStockMovementInRequest;
use Core\Inventory\Application\UseCases\OrderItemCompletedUpdate;
use Core\Inventory\Application\UseCases\AdjustmentUpdateInventory;
use Core\Inventory\Application\UseCases\OrderItemCancelledUpdate;
use Core\Inventory\Application\UseCases\UpdateInventoryById;
use Core\Inventory\Application\UseCases\UpdateInventoryByStockMovementIn;
use Illuminate\Support\Facades\Event;

class InventoryListener
{
    public function handle(
        UpdateInventoryById $UpdateInventoryById,
        UpdateInventoryByStockMovementIn $UpdateInventoryByStockMovementIn,
        OrderItemCompletedUpdate $OrderItemCompletedUpdate,
        AdjustmentUpdateInventory $AdjustmentUpdateInventory,
        OrderItemCancelledUpdate $OrderItemCancelledUpdate
    ) {
        Event::listen(
            "erp.stockmovementin.*",
            function (string $eventName, array $data) use (
                $UpdateInventoryByStockMovementIn
            ) {
                if ($eventName === 'erp.stockmovementin.completed') {
                    $UpdateInventoryByStockMovementIn->handle($data);
                }
            }
        );
        Event::listen(
            "erp.inventoryadjustment.*",
            function (string $eventName, array $data) use($AdjustmentUpdateInventory) {
                if($eventName === 'erp.inventoryadjustment.create') {
                    $AdjustmentUpdateInventory
                        ->handle([
                            ...$data,
                            'quantity' => $data['qty_adjusted']
                        ]);
                }
            }
        );

        Event::listen('erp.orderitem.*',
            function(string $eventName, array $data) 
                use($OrderItemCompletedUpdate,
                    $UpdateInventoryById,
                    $OrderItemCancelledUpdate) {
                if($eventName === 'erp.orderitem.completed') {
                   $OrderItemCompletedUpdate
                    ->handle($data);
                }
                if($eventName === 'erp.orderitem.cancelled') {
                   logs()->info("Order item cancelled event received in InventoryListener", $data);
                   $OrderItemCancelledUpdate
                    ->handle($data);
                }
                if($eventName === 'erp.orderitem.create' || $eventName === 'erp.orderitem.delete'
                || $eventName === 'erp.orderitem.update') {
                   $UpdateInventoryById
                    ->handle([
                        'id' => $data['inventory_id'],
                        'reserved_qty' => $data['qty_change'],
                        'user_id' => $data['user_id'],
                        'business_id' => $data['business_id']
                    ]);
                } 
            });
    }
}

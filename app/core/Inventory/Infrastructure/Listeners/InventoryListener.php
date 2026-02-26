<?php

namespace Core\Inventory\Infrastructure\Listeners;

use Core\Inventory\Application\UseCases\OrderItemCompletedUpdate;
use Core\Inventory\Application\UseCases\AdjustmentUpdateInventory;
use Core\Inventory\Application\UseCases\OrderItemCancelledUpdate;
use Core\Inventory\Application\UseCases\UpdateInventory;
use Core\Inventory\Application\UseCases\UpdateInventoryById;
use Core\Inventory\Application\UseCases\UpdateInventoryByStockMovementIn;
use Illuminate\Support\Facades\Event;

class InventoryListener
{
    public function __construct(
        private UpdateInventoryById $UpdateInventoryById,
        private UpdateInventoryByStockMovementIn $UpdateInventoryByStockMovementIn,
        private OrderItemCompletedUpdate $OrderItemCompletedUpdate,
        private AdjustmentUpdateInventory $AdjustmentUpdateInventory,
        private OrderItemCancelledUpdate $OrderItemCancelledUpdate,
        private UpdateInventory $updateInventory
    )
    {
        
    }
    public function handle() {
        Event::listen(
            "erp.stockmovementin.*",
            function (string $eventName, array $data) {
                if ($eventName === 'erp.stockmovementin.completed') {
                    $this->UpdateInventoryByStockMovementIn->handle($data);
                }
            }
        );
        Event::listen(
            "erp.stockmovementout.*",
            function (string $eventName, array $data) {
                if ($eventName === 'erp.stockmovementout.update') {
                    $this->updateInventory->handle($data);
                }
            }
        );
        Event::listen(
            "erp.inventoryadjustment.*",
            function (string $eventName, array $data) {
                if($eventName === 'erp.inventoryadjustment.create') {
                    $this->AdjustmentUpdateInventory
                        ->handle([
                            ...$data,
                            'quantity' => $data['qty_adjusted']
                        ]);
                }
            }
        );

        Event::listen('erp.orderitem.*',
            function(string $eventName, array $data) {
                if($eventName === 'erp.orderitem.completed') {
                   $this->OrderItemCompletedUpdate
                    ->handle($data);
                }
                if($eventName === 'erp.orderitem.cancelled') {
                   $this->OrderItemCancelledUpdate
                    ->handle($data);
                }
                if($eventName === 'erp.orderitem.create' || $eventName === 'erp.orderitem.delete'
                || $eventName === 'erp.orderitem.update') {
                   $this->UpdateInventoryById
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

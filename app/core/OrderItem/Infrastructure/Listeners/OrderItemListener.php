<?php

namespace Core\OrderItem\Infrastructure\Listeners;

use Core\OrderItem\Application\DTOs\CheckExistsOrderItemRequest;
use Core\OrderItem\Application\UseCases\CancelledOrderItem;
use Core\OrderItem\Application\UseCases\CheckExistsOrderItem;
use Core\OrderItem\Application\UseCases\CompletedOrderItem;
use Core\OrderItem\Application\UseCases\GetSummaryOrderItem;
use Illuminate\Support\Facades\Event;

class OrderItemListener
{
     public function handle(CompletedOrderItem $CompletedOrderItem,
          CancelledOrderItem $CancelledOrderItem,
          CheckExistsOrderItem $CheckExistsOrderItem,
          GetSummaryOrderItem $getSummaryOrderItem)
     {
          Event::listen(
               'erp.stockout.*',
               function (string $eventName, array $data)
               use ($CompletedOrderItem) {
                    if ($eventName === 'erp.stockout.completed') {
                         $CompletedOrderItem->handle([
                              ...$data,
                              'stock_out_id' => $data['id']
                         ]);
                    }
               }
          );
          Event::listen(
               'erp.order.*',
               function (string $eventName, array $data)
               use ($CancelledOrderItem,$CheckExistsOrderItem,$getSummaryOrderItem) {
                    if ($eventName === 'erp.order.cancelled') {
                         $CancelledOrderItem->handle($data);
                    } else if ($eventName === 'erp.order.approved') {
                         $CheckExistsOrderItem->handle(
                              CheckExistsOrderItemRequest::fromArray($data)
                         );
                         $getSummaryOrderItem->handle([
                              'business_id' => $data['business_id'],
                              'user_id' => $data['user_id'],
                              'order_id' => $data['order_id']
                         ]);
                    }
               }
          );
     }
}

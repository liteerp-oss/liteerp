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
     function __construct(private CompletedOrderItem $CompletedOrderItem,
          private CancelledOrderItem $CancelledOrderItem,
          private CheckExistsOrderItem $CheckExistsOrderItem,
          private GetSummaryOrderItem $getSummaryOrderItem)
     {
          
     }
     public function handle()
     {
          Event::listen(
               'erp.stockout.*',
               function (string $eventName, array $data){
                    if ($eventName === 'erp.stockout.completed') {
                         $this->CompletedOrderItem->handle([
                              ...$data,
                              'stock_out_id' => $data['id']
                         ]);
                    }
               }
          );
          Event::listen(
               'erp.order.*',
               function (string $eventName, array $data) {
                    if ($eventName === 'erp.order.cancelled') {
                         $this->CancelledOrderItem->handle($data);
                    } else if ($eventName === 'erp.order.approved') {
                         $this->CheckExistsOrderItem->handle(
                              CheckExistsOrderItemRequest::fromArray($data)
                         );
                         $this->getSummaryOrderItem->handle($data);
                    }
               }
          );
          
     }
}

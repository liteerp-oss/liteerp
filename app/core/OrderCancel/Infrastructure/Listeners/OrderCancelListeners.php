<?php 
namespace Core\OrderCancel\Infrastructure\Listeners;

use Core\OrderCancel\Application\DTOs\CreateOrderCancelRequest;
use Core\OrderCancel\Application\UseCases\CreateOrderCancel;
use Illuminate\Support\Facades\Event;

class OrderCancelListeners {
    function __construct(private CreateOrderCancel $CreateOrderCancel)
    {
        
    }
    public function handle(){
        Event::listen('erp.order.*',function(string $eventName, array $data) {
            if($eventName === 'erp.order.cancelled') {
                $this->CreateOrderCancel->handle(CreateOrderCancelRequest::fromArray($data));
            }
        });
    }
}
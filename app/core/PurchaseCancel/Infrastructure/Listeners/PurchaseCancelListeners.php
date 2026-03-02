<?php 
namespace Core\PurchaseCancel\Infrastructure\Listeners;

use Core\PurchaseCancel\Application\DTOs\CreatePurchaseCancelRequest;
use Core\PurchaseCancel\Application\UseCases\CreatePurchaseCancel;
use Illuminate\Support\Facades\Event;

class PurchaseCancelListeners {
    function __construct(private CreatePurchaseCancel $CreatePurchaseCancel)
    {
        
    }
    public function handle(){
        Event::listen('erp.purchase.*',function(string $eventName, array $data) {
            if($eventName === 'erp.purchase.cancelled') {
                
                $this->CreatePurchaseCancel->handle($data);
            }
        });
    }
}
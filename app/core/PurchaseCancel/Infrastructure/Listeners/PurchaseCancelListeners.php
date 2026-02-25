<?php 
namespace Core\PurchaseCancel\Infrastructure\Listeners;

use Core\PurchaseCancel\Application\DTOs\CreatePurchaseCancelRequest;
use Core\PurchaseCancel\Application\UseCases\CreatePurchaseCancel;
use Illuminate\Support\Facades\Event;

class PurchaseCancelListeners {
    public function handle(CreatePurchaseCancel $CreatePurchaseCancel){
        Event::listen('erp.purchase.*',function(string $eventName, array $data) use($CreatePurchaseCancel) {
            if($eventName === 'erp.purchase.cancelled') {
                
                $CreatePurchaseCancel->handle($data);
            }
        });
    }
}
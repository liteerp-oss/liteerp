<?php

namespace Core\StockIn\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;
use Core\StockIn\Application\DTOs\CancelledStockInRequest;
use Core\StockIn\Application\DTOs\CreateStockInRequest;
use Core\StockIn\Domain\Services\StockInService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class CancelledStockIn {
    public function __construct(private StockInService $service) {}

    public function handle(array $data)
    {
        $dto = CancelledStockInRequest::fromArray($data);
        $stock = $this->service->getByInvoiceInId($dto->toArray());
        if(!$stock) {
            /**
             * Because system don't automatic create stock in 
             * So if stock is not found then stop continue
             */
            return true;
        }
        DB::beginTransaction();
        $update = $this->service->changeToCancelled($dto->toArray());
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'cancelled',
            'entity_type' => 'stockin',
            'entity_id' => $update->id,
            'chanels' => ['db'],
            'message' => "stockin::messages.notification.cancelled",
            'message_params' => [
                'username' => $data['username']
            ]
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::PURCHASE_APPROVED,
                Permission::PURCHASE_CANCELLED,
                Permission::STOCKIN_CANCELLED,
                Permission::STOCKIN_RECEIVED,
                Permission::STOCKIN_UPDATE,
                Permission::INVOICEIN_APPROVED
            ]
        ]);
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
        DB::commit();
        return $update;
    }
}
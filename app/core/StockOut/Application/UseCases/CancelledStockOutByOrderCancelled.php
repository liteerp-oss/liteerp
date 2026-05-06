<?php

namespace Core\StockOut\Application\UseCases;

use App\Exceptions\BadException;
use App\Supports\Permissions\Enums\Permission;
use Core\StockOut\Application\DTOs\CancelledStockOutByOrderCancelledRequest;
use Core\StockOut\Domain\Services\StockOutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CancelledStockOutByOrderCancelled
{
    public function __construct(
        private StockOutService $service,
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CancelledStockOutByOrderCancelledRequest::fromArray($data);
        $entity = $this->service->getByInvoiceOutId($dto->toArray());
        if ($entity) {
            if ($entity->isCompleted()) {
                throw new BadException(__("stockout::messages.order_completed_cannot_cancel"));
            }
            if($entity->isShipped()) {
                throw new BadException(__("stockout::messages.order_shipped_cannot_cancel"));
            }
            $entity->markCancelled();
            $update = $this->service->update([
                ...$entity->toArray(),
                'order_id' => $dto->order_id
            ]);
            Event::dispatch(Permission::STOCKOUT_CANCELLED->value, [
                ...$data,
                ...$update->toArray(),
                'stock_out_id' => $update->id
            ]);
            $notification = [
                'user_id' => $dto->created_by,
                'business_id' => $dto->business_id,
                'type' => $update->getStatus(),
                'entity_type' => 'stockout',
                'entity_id' => $update->id,
                'chanels' => ['db'],
                'message' => "stockout::messages.notification.{$update->getStatus()}",
                'message_params' => [
                    'username' => $data['username']
                ]
            ];
            Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
                ...$notification,
                'permissions' => [
                    Permission::STOCKOUT_SHIPPED->value,
                    Permission::STOCKOUT_COMPLETED->value,
                    Permission::STOCKOUT_UPDATE->value,
                    Permission::STOCKOUT_CREATE->value,
                    Permission::ORDER_APPROVED->value,
                    Permission::ORDER_CANCELLED->value,
                    Permission::ORDER_CREATE->value,
                    Permission::ORDER_UPDATE->value,
                    Permission::INVOICEOUT_APPROVED->value,
                    Permission::INVOICEOUT_UNAPPROVED->value,
                    Permission::INVOICEOUT_UPDATE->value
                ]
            ]);
        }

        DB::commit();
    }
}

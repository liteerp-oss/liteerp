<?php

namespace Core\StockOut\Application\UseCases;

use App\Exceptions\BadException;
use Core\StockOut\Application\DTOs\CancelledStockOutByOrderCancelledRequest;
use Core\StockOut\Domain\Services\StockOutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CancelledStockOutByOrderCancelled
{
    public function __construct(
        private StockOutService $service,
    ) {}

    public function handle(CancelledStockOutByOrderCancelledRequest $dto)
    {
        DB::beginTransaction();
        $entity = $this->service->getByInvoiceInId($dto->toArray());
        if ($entity) {
            if($entity->isCompleted()) {
                throw new BadException(__("stockout::messages.order_completed_cannot_cancel"));
            }
            $entity->markCancelled();
            $update = $this->service->update($entity->toArray());
            Event::dispatch("erp.stockout.cancelled", [
                ...$update->toArray(),
                'user_id' => $dto->created_by,
                'business_id' => $dto->business_id,
                'order_id' => $dto->order_id,
                'stock_out_id' => $update->id
            ]);
        }
        DB::commit();
    }
}

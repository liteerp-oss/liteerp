<?php

namespace Core\StockMovementOut\Application\UseCases;

use Core\Inventory\Application\UseCases\UpdateInventory;
use Core\StockMovementOut\Application\DTOs\CreateStockMovementOutRequest;
use Core\StockMovementOut\Domain\Services\StockMovementOutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateStockMovementOut
{
    public function __construct(private StockMovementOutService $service,
    private UpdateInventory $updateInventory) {}

    public function handle(CreateStockMovementOutRequest $dto)
    {
        DB::beginTransaction(); 
        $update = $this->service->update($dto->toArray());
        // update inventory 
        $qty_change = $update->qty_change - $dto->qty_change;
        $this->updateInventory->handle([
            'product_id' => $dto->product_id,
            'warehouse_id'  => $dto->warehouse_id,
            'reserved_quantity' => $qty_change,
            'business_id' => $dto->business_id
        ]);
        Event::dispatch("erp.stockmovementout.update", [
            ...$update->toArray(),
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id
        ]);
        DB::commit();
        return $update;
    }
}
<?php

namespace Core\StockMovementOut\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use Core\StockMovementOut\Application\DTOs\CreateManyStockMovementOutRequest;
use Core\StockMovementOut\Application\DTOs\CreateStockMovementOutRequest;
use Core\StockMovementOut\Domain\Services\StockMovementOutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateManyStockMovementOut
{
    public function __construct(
        private StockMovementOutService $service
    ) {}

    public function handle(CreateManyStockMovementOutRequest $dto)
    {
        DB::beginTransaction();
        foreach ($dto->list as $key => $value) {
            $adapter = CreateStockMovementOutRequest::fromArray([
                'business_id' => $dto->business_id,
                'user_id'   => $dto->created_by,
                'order_item_id' => $value['order_item_id']
            ]);
            $create = $this->service->create($adapter->toArray());
            Event::dispatch(Permission::STOCKMOVEMENTOUT_CREATE->value,[
                ...$create->toArray(),
                'business_id' => $dto->business_id,
                'user_id' => $dto->created_by,
                'order_id' => $dto->order_id
            ]);
        }
        DB::commit();
        return $create;
    }
}

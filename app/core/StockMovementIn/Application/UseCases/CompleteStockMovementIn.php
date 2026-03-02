<?php

namespace Core\StockMovementIn\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Exceptions\BadException;
use Core\StockMovementIn\Application\DTOs\CompleteStockMovementInRequest;
use Core\StockMovementIn\Domain\Services\StockMovementInService;
use Illuminate\Support\Facades\Event;

class CompleteStockMovementIn
{
    public function __construct(private StockMovementInService $service) {}
    public function handle(array $data)
    {
        $dto = CompleteStockMovementInRequest::fromArray($data);
        $list = $this->service->index($dto->toArray());
        if(intval($list['total']) === 0) {
            throw new BadException(__("stockmovementin::messages.no_inventory_added"));
        }
        Event::dispatch(Permission::STOCKMOVEMENTIN_COMPLETED->value, [
            'stock_in_id' => $dto->stock_in_id,
            'business_id' => $dto->business_id,
            'user_id' => $dto->created_by,
            'list' => $list['data']
        ]);
        return $list;
    }
}

<?php

namespace Core\StockMovementIn\Application\UseCases;

use App\Exceptions\BadException;
use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\StockMovementIn\Application\DTOs\CheckStockMovementInForInventoryAdjustmentRequest;
use Core\StockMovementIn\Application\DTOs\CheckStockMovementInRequest;
use Core\StockMovementIn\Domain\Services\StockMovementInService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CheckStockMovementInForInventoryAdjustment
{
    public function __construct(
        private StockMovementInService $service
    ) {

    }
    function handle(array $data) {
        $dto = CheckStockMovementInForInventoryAdjustmentRequest::fromArray($data);
        logs()->debug("CheckStockMovementInForInventoryAdjustment",$dto->toArray());
        $row = $this->service->showWithAvailabelQtyChange($dto->toArray());
        $quantity = $row['quantity'] + $dto->qty_adjusted;
        if($quantity < 0) {
            throw new BadException(__("stockmovementin::messages.batch_is_not_enought_inventory"));
        }
    }
}
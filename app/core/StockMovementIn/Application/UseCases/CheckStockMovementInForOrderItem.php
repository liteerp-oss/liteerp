<?php

namespace Core\StockMovementIn\Application\UseCases;

use App\Exceptions\BadException;
use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\StockMovementIn\Application\DTOs\CheckStockMovementInRequest;
use Core\StockMovementIn\Domain\Services\StockMovementInService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CheckStockMovementInForOrderItem
{
    public function __construct(
        private StockMovementInService $service
    ) {}

    public function handle(array $data)
    {
        if ($data['qty_change'] <= 0) {
            /**
             * on case we sum again
             */
            return;
        }
        /**
         * on case we get more
         */
        $dto = CheckStockMovementInRequest::fromArray($data);
        $show = $this->service->showWithAvailabelQtyChange($dto->toArray());
        if($data['qty_change'] > $show['quantity']) {
            throw new BadException(__("stockmovementin::messages.batch_is_not_enought_inventory"));
        }
        return $data;
    }
}

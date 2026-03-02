<?php

namespace Core\StockMovementIn\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\StockMovementIn\Application\DTOs\CreateStockMovementInRequest;
use Core\StockMovementIn\Domain\Services\StockMovementInService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
class CreateStockMovementIn
{
    public function __construct(
        private StockMovementInService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateStockMovementInRequest::fromArray($data);
        $data = $this->hooks->dispatch(
                new HookContext(
                    action: HookAction::CREATE,
                    phase: HookPhase::RESPONSE,
                    timing: HookTiming::BEFORE,
                    payload: [
                        ...$data,
                        ...$dto->toArray()
                    ],
                    module: 'StockMovementIn'
                )
            );
        $create = $this->service->create($dto->toArray());
        $data = $this->hooks->dispatch(
                new HookContext(
                    action: HookAction::CREATE,
                    phase: HookPhase::RESPONSE,
                    timing: HookTiming::AFTER,
                    payload: [
                        ...$data,
                        ...$create->toArray()
                    ],
                    module: 'StockMovementIn'
                )
            );
        Event::dispatch(Permission::STOCKMOVEMENTIN_CREATE->value, [
            ...$data
        ]);
        DB::commit();
        return $data;
    }
}

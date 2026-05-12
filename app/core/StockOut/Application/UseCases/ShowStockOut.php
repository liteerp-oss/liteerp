<?php

namespace Core\StockOut\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\StockOut\Application\DTOs\ShowStockOutRequest;
use Core\StockOut\Domain\Services\StockOutService;
use Illuminate\Support\Facades\Event;

class ShowStockOut
{
    public function __construct(private StockOutService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data) : array
    {
        $dto = ShowStockOutRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'StockOut'
            )
        );
        $show = $this->service->show($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$show
                ],
                module: 'StockOut'
            )
        );
        // Event::dispatch(Permission::STOCKOUT_SHOW->value, [
        //     ...$data
        // ]);
        return $data;
    }
}
<?php

namespace Core\StockIn\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\StockIn\Application\DTOs\ShowStockInRequest;
use Core\StockIn\Domain\Services\StockInService;

class ShowStockIn
{
    public function __construct(private StockInService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        $dto = ShowStockInRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'StockIn'
            )
        );
        $stock = $this->service->show($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$stock
                ],
                module: 'StockIn'
            )
        );
        return $data;
    }
}
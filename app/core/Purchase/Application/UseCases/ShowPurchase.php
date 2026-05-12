<?php

namespace Core\Purchase\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Purchase\Application\DTOs\ShowPurchaseRequest;
use Core\Purchase\Domain\Services\PurchaseService;
use Core\Purchase\Domain\Entities\Purchase;
use Illuminate\Support\Facades\Event;

class ShowPurchase
{
    public function __construct(private PurchaseService $service,
    private HookDispatcher $hooks) {}

    public function handle(array $data): array
    {
        $dto = ShowPurchaseRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Purchase'
            )
        );
        $show = $this->service->show($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$show
                ],
                module: 'Purchase'
            )
        );
        // Event::dispatch(Permission::PURCHASE_SHOW->value, [
        //     ...$data
        // ]);
        return $data;
    }
}

<?php

namespace Core\PurchaseItem\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\PurchaseItem\Application\DTOs\DeletePurchaseItemRequest;
use Core\PurchaseItem\Domain\Services\PurchaseItemService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DeletePurchaseItem
{
    public function __construct(
        private PurchaseItemService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeletePurchaseItemRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'PurchaseItem'
            )
        );
        $item = $this->service->delete($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$item->toArray()
                ],
                module: 'PurchaseItem'
            )
        );
        Event::dispatch(Permission::PURCHASEITEM_DELETE->value,[
            ...$data
        ]);
        DB::commit();
        return $data;
    }
}

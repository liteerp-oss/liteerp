<?php

namespace Core\OrderItem\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use Core\OrderItem\Application\DTOs\DeleteOrderItemRequest;
use Core\OrderItem\Domain\Services\OrderItemService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;

class DeleteOrderItem
{
    public function __construct(private OrderItemService $service, private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeleteOrderItemRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'OrderItem'
            )
        );
        $item = $this->service->delete($dto->toArray());
        $qty_change = (float) ($item->buy_quantity
            + $item->gift_quantity
            + $item->compensation_quantity
            + $item->conversion_quantity);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$item->toArray()
                ],
                module: 'OrderItem'
            )
        );
        Event::dispatch(Permission::ORDERITEM_DELETE->value, [
            ...$data,
            'qty_change' => -abs($qty_change),
        ]);
        DB::commit();
        return $data;
    }
}

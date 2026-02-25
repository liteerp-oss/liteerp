<?php

namespace Core\Purchase\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Purchase\Application\DTOs\UpdatePurchaseRequest;
use Core\Purchase\Domain\Services\PurchaseService;
use Core\Purchase\Domain\Entities\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdatePurchase
{
    public function __construct(private PurchaseService $service,
    private HookDispatcher $hooks) {}

    public function handle(array $data): array
    {
        DB::beginTransaction();
        $dto = UpdatePurchaseRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Purchase'
            )
        );
        $update = $this->service->update($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$update->toArray()
                ],
                module: 'Purchase'
            )
        );
        if($update->isApproved()) {
            $forInvoice = $this->service->show($dto->toArray());
            $updateData = [
                ...$data,
                ...$forInvoice
            ];
            Event::dispatch("erp.purchase.approved", $updateData);
        } else if($update->isDraft()) {
            $updateData = [
                ...$data,
            ];
            Event::dispatch("erp.purchase.update", $updateData);
        } else if($update->isRequested()) {
            $updateData = [
                ...$data,
            ];
            Event::dispatch("erp.purchase.requested", $updateData);
        } else if($update->isCancelled()){
            $updateData = [
                ...$data,
            ];
            Event::dispatch("erp.purchase.cancelled", $updateData);
        }
        Event::dispatch("erp.notification.many", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $update->getStatus(),
            'entity_type' => 'purchase',
            'entity_id' => $update->id,
            'chanels' => ['db']
        ]);
        Event::dispatch("erp.notification.create", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $update->getStatus(),
            'entity_type' => 'purchase',
            'entity_id' => $update->id,
            'chanels' => ['db']
        ]);
        DB::commit();
        return $data;
    }
}

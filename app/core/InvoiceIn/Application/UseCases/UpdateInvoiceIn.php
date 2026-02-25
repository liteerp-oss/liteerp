<?php

namespace Core\InvoiceIn\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\InvoiceIn\Application\DTOs\CreateInvoiceInRequest;
use Core\InvoiceIn\Domain\Services\InvoiceInService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateInvoiceIn
{
    public function __construct(
        private InvoiceInService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: $data,
                module: 'InvoiceIn'
            )
        );
        $dto = CreateInvoiceInRequest::fromArray($data);
        $entity = $this->service->findById($dto->toArray());
        $update = $this->service->update($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$update->toArray()
                ],
                module: 'InvoiceIn'
            )
        );
        $status = 'update';
        if($update->isApproved() && !$entity->isApproved()) {
            Event::dispatch("erp.invoicein.approved", [
                ...$update->toArray(),
                'user_id' => $dto->created_by,
                'business_id' => $dto->business_id,
                'invoice_in_id' => $update->id,
            ]);
            $status = 'approved';
        } else {
            Event::dispatch("erp.invoicein.update", [
                ...$update->toArray(),
                'user_id' => $dto->created_by,
                'business_id' => $dto->business_id,
                'invoice_in_id' => $update->id,
            ]);
        }
        Event::dispatch("erp.notification.many", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $status,
            'entity_type' => 'invoicein',
            'entity_id' => $update->id,
            'chanels' => ['db']
        ]);
        Event::dispatch("erp.notification.create", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => $status,
            'entity_type' => 'invoicein',
            'entity_id' => $update->id,
            'chanels' => ['db']
        ]);
        
        DB::commit();
        return $update;
    }
}

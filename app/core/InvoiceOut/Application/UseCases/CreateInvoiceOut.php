<?php

namespace Core\InvoiceOut\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\InvoiceOut\Application\DTOs\CreateInvoiceOutRequest;
use Core\InvoiceOut\Domain\Services\InvoiceOutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateInvoiceOut
{
    public function __construct(
        private InvoiceOutService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateInvoiceOutRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'InvoiceOut'
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
                module: 'InvoiceOut'
            )
        );
        Event::dispatch("erp.invoiceout.create", [
            ...$data
        ]);
        Event::dispatch("erp.notification.many", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'created',
            'entity_type' => 'stockout',
            'entity_id' => $create->id,
            'chanels' => ['db'],
            'roles' => ['admin', 'manager']
        ]);
        Event::dispatch("erp.notification.create", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'created',
            'entity_type' => 'stockout',
            'entity_id' => $create->id,
            'chanels' => ['db']
        ]);
        DB::commit();
        return $data;
    }
}

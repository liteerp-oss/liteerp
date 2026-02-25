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

class AutomaticCreateInvoice
{
    public function __construct(private InvoiceInService $service,
    private HookDispatcher $hooks) {}

    public function handle(CreateInvoiceInRequest $dto)
    {
        DB::beginTransaction();
        if($this->service->getByPurchaseId($dto->toArray())) {
            return;
        }
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: $dto->toArray(),
                module: 'InvoiceIn'
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
                module: 'InvoiceIn'
            )
        );
        Event::dispatch('erp.invoicein.create',[
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            ...$create->toArray()
        ]);
        Event::dispatch("erp.notification.many", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'created',
            'entity_type' => 'invoicein',
            'entity_id' => $create->id,
            'chanels' => ['db']
        ]);
        Event::dispatch("erp.notification.create", [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'created',
            'entity_type' => 'invoicein',
            'entity_id' => $create->id,
            'chanels' => ['db']
        ]);
        DB::commit();
        return $create;
    }
}
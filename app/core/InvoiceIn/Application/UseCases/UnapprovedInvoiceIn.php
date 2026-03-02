<?php

namespace Core\InvoiceIn\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\InvoiceIn\Application\DTOs\ChangeToUnapprovedRequest;
use Core\InvoiceIn\Application\DTOs\CreateInvoiceInRequest;
use Core\InvoiceIn\Domain\Services\InvoiceInService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UnapprovedInvoiceIn
{
    public function __construct(
        private InvoiceInService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(ChangeToUnapprovedRequest $dto)
    {
    
        $invoice = $this->service->getByPurchaseId($dto->toArray());
        if(!$invoice) {
            /**
             * If System don't automatic create invoice 
             * So we don't need continue 
             */
            return;
        }
        /**
         * Is UnApproved 
         */
        if(!$invoice->isApproved()) {
            return;
        }
        DB::beginTransaction();
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: $dto->toArray(),
                module: 'InvoiceIn'
            )
        );
        $update = $this->service->changeToUnApproved($dto->toArray());
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
        Event::dispatch(Permission::INVOICEIN_CANCELLED->value, [
            ...$data,
            'invoice_in_id' => $update->id,
        ]);
        DB::commit();
        return $update;
    }
}

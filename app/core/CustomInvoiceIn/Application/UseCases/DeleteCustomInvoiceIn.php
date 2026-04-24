<?php

namespace Core\CustomInvoiceIn\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\CustomInvoiceIn\Application\DTOs\DeleteCustomInvoiceInRequest;
use Core\CustomInvoiceIn\Domain\Services\CustomInvoiceInService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DeleteCustomInvoiceIn
{
    public function __construct(private CustomInvoiceInService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeleteCustomInvoiceInRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'CustomInvoiceIn'
            )
        );
        
        $delete = $this->service->delete($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$delete->toArray()
                ],
                module: 'CustomInvoiceIn'
            )
        );
        Event::dispatch(Permission::CUSTOMINVOICEIN_DELETE->value, [
            ...$data
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.delete', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'custominvoicein',
            'id' => $delete->id,
            'data' => $delete->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}
<?php

namespace Core\CustomInvoiceIn\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\CustomInvoiceIn\Application\DTOs\CreateCustomInvoiceInRequest;
use Core\CustomInvoiceIn\Domain\Services\CustomInvoiceInService;
use Core\CustomInvoiceIn\Infrastructure\Events\CustomInvoiceInEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateCustomInvoiceIn
{
    public function __construct(private CustomInvoiceInService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateCustomInvoiceInRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'CustomInvoiceIn'
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
                module: 'CustomInvoiceIn'
            )
        );
        Event::dispatch(Permission::CUSTOMINVOICEIN_UPDATE->value, [
            ...$data
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.update', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'custominvoicein',
            'id' => $update->id,
            'data' => $update->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}
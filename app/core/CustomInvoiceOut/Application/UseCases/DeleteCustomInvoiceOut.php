<?php

namespace Core\CustomInvoiceOut\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\CustomInvoiceOut\Application\DTOs\DeleteCustomInvoiceOutRequest;
use Core\CustomInvoiceOut\Domain\Services\CustomInvoiceOutService;
use Core\CustomInvoiceOut\Infrastructure\Events\CustomInvoiceOutEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DeleteCustomInvoiceOut
{
    public function __construct(private CustomInvoiceOutService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeleteCustomInvoiceOutRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'CustomInvoiceOut'
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
                module: 'CustomInvoiceOut'
            )
        );
        Event::dispatch(Permission::CUSTOMINVOICEOUT_DELETE->value, [
            ...$data
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.delete', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'custominvoiceout',
            'id' => $delete->id,
            'data' => $delete->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}
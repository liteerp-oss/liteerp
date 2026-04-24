<?php

namespace Core\Supplier\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Supplier\Application\DTOs\DeleteSupplierRequest;
use Core\Supplier\Domain\Services\SupplierService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DeleteSupplier
{
    public function __construct(private SupplierService $service,
    private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeleteSupplierRequest::fromArray($data); 
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Supplier'
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
                module: 'Supplier'
            )
        );
        Event::dispatch(Permission::SUPPLIER_DELETE->value, [
            ...$data,
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.delete', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'supplier',
            'id' => $delete->id,
            'data' => $delete->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}
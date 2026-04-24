<?php

namespace Core\Supplier\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Supplier\Application\DTOs\CreateSupplierRequest;
use Core\Supplier\Domain\Services\SupplierService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class UpdateSupplier
{
    public function __construct(private SupplierService $service,
    private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateSupplierRequest::fromArray($data); 
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::UPDATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Supplier'
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
                module: 'Supplier'
            )
        );
        Event::dispatch(Permission::SUPPLIER_UPDATE->value, [
            ...$data
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.update', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'supplier',
            'id' => $update->id,
            'data' => $update->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}
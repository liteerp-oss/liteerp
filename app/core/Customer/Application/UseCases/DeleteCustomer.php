<?php

namespace Core\Customer\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\Customer\Application\DTOs\DeleteCustomerRequest;
use Core\Customer\Domain\Services\CustomerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DeleteCustomer
{
    public function __construct(private CustomerService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeleteCustomerRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Customer'
            )
        );
        $update = $this->service->delete($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$update->toArray()
                ],
                module: 'Customer'
            )
        );
        Event::dispatch(Permission::CUSTOMER_DELETE->value, [
            ...$data
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.delete', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'customer',
            'id' => $update->id,
            'data' => $update->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}
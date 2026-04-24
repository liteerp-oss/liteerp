<?php

namespace Core\CustomerGroup\Application\UseCases;

use App\Jobs\CreateNotificationJob;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\CustomerGroup\Application\DTOs\CreateCustomerGroupRequest;
use Core\CustomerGroup\Domain\Services\CustomerGroupService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateCustomerGroup
{
    public function __construct(private CustomerGroupService $service,
    private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateCustomerGroupRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'CustomerGroup'
            )
        );
        $create = $this->service->create($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$create->toArray()
                ],
                module: 'CustomerGroup'
            )
        );
        Event::dispatch(Permission::CUSTOMERGROUP_CREATE->value, [
            ...$data
        ]);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.create', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'customergroup',
            'id' => $create->id,
            'data' => $create->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}

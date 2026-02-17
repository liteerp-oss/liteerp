<?php

namespace Core\CustomerGroup\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\CustomerGroup\Application\DTOs\DeleteCustomerGroupRequest;
use Core\CustomerGroup\Domain\Services\CustomerGroupService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DeleteCustomerGroup
{
    public function __construct(
        private CustomerGroupService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = DeleteCustomerGroupRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'CustomerGroup'
            )
        );
        $delete = $this->service->delete($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::DELETE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$delete->toArray()
                ],
                module: 'CustomerGroup'
            )
        );
        Event::dispatch("erp.customergroup.delete", [
            ...$data
        ]);
        DB::commit();
        return $data;
    }
}

<?php

namespace Core\CustomerGroup\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\CustomerGroup\Application\DTOs\ShowCustomerGroupRequest;
use Core\CustomerGroup\Domain\Services\CustomerGroupService;
use Illuminate\Support\Facades\Event;

class ShowCustomerGroup
{
    public function __construct(
        private CustomerGroupService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        $dto = ShowCustomerGroupRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'CustomerGroup'
            )
        );
        $show = $this->service->show($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$show->toArray()
                ],
                module: 'CustomerGroup'
            )
        );
        // Event::dispatch(Permission::CUSTOMERGROUP_SHOW->value,[
        //     ...$data
        // ]);
        return $data;
    }
}

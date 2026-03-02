<?php

namespace Core\Overview\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Overview\Application\DTOs\IndexOverviewRequest;
use Core\Overview\Domain\Services\OverviewService;
use Illuminate\Support\Facades\Event;

class IndexOverview
{
    public function __construct(private OverviewService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        $dto = IndexOverviewRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'Overview'
            )
        );
        $index = $this->service->index($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::INDEX,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$index
                ],
                module: 'Overview'
            )
        );
        Event::dispatch(Permission::OVERVIEW_INDEX->value, [
            ...$data
        ]);
        return $data;
    }
}

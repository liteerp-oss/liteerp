<?php

namespace Core\InvoiceIn\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\InvoiceIn\Application\DTOs\CreateInvoiceInRequest;
use Core\InvoiceIn\Application\DTOs\ShowInvoiceInRequest;
use Core\InvoiceIn\Domain\Services\InvoiceInService;
use Illuminate\Support\Facades\Event;

class ShowInvoiceIn
{
    public function __construct(private InvoiceInService $service,
        private HookDispatcher $hooks) {}

    public function handle(array $data)
    {
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: $data,
                module: 'InvoiceIn'
            )
        );
        $dto = ShowInvoiceInRequest::fromArray($data);
        Event::dispatch(Permission::INVOICEIN_SHOW->value,[
            ...$dto->toArray(),
            'user_id' => $dto->created_by
        ]);
        $show = $this->service->show($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::SHOW,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$show
                ],
                module: 'InvoiceIn'
            )
        );

        return $data;

    }
}
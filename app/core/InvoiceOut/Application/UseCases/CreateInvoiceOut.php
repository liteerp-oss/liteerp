<?php

namespace Core\InvoiceOut\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use App\Supports\Permissions\Enums\Permission;
use Core\InvoiceOut\Application\DTOs\CreateInvoiceOutRequest;
use Core\InvoiceOut\Domain\Services\InvoiceOutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateInvoiceOut
{
    public function __construct(
        private InvoiceOutService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateInvoiceOutRequest::fromArray($data);
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    ...$dto->toArray()
                ],
                module: 'InvoiceOut'
            )
        );
        
        $create = $this->service->create($dto->toArray());
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::AFTER,
                payload: [
                    ...$data,
                    ...$create->toArray()
                ],
                module: 'InvoiceOut'
            )
        );
        Event::dispatch(Permission::INVOICEOUT_CREATE->value, [
            ...$data
        ]);
        $notification = [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'created',
            'entity_type' => 'invoiceout',
            'entity_id' => $create->id,
            'chanels' => ['db'],
            'title' => "invoiceout::messages.title",
            'message' => "invoiceout::messages.notification.created",
            'message_params' => [
                'username' => $data['username']
            ]
        ];
        Event::dispatch(Permission::NOTIFICATION_CREATE_MANY->value, [
            ...$notification,
            'permissions' => [
                Permission::INVOICEOUT_APPROVED->value,
                Permission::INVOICEOUT_UPDATE->value,
                Permission::INVOICEIN_CANCELLED->value
            ]
        ]);
        Event::dispatch(Permission::NOTIFICATION_CREATE->value, $notification);
        /**
         * Activity log 
         */
        Event::dispatch('erp.activitylog.create', [
            'user_id' => $dto->created_by,
            'business_id' => $dto->business_id,
            'type' => 'invoiceout',
            'id' => $create->id,
            'data' => $create->toArray()        
        ]);
        DB::commit();
        return $data;
    }
}

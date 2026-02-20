<?php

namespace Core\Permission\Application\UseCases;

use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Core\Permission\Application\DTOs\CreateFullPermissionRequest;
use Core\Permission\Domain\Services\PermissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateFullPermission
{
    public function __construct(
        private PermissionService $service,
        private HookDispatcher $hooks
    ) {}

    public function handle(array $data)
    {
        DB::beginTransaction();
        $dto = CreateFullPermissionRequest::fromArray($data);
        $config = config('permission.permissions');
        $permissions = [
            ...$config['user'],
            ...$config['customer'],
            ...$config['customer_group'],
            ...$config['supplier'],
            ...$config['pricelist'],
            ...$config['shipping'],
            ...$config['order'],
            ...$config['order_shipping'],
            ...$config['order_item'],
            ...$config['product'],
            ...$config['category_product'],
            ...$config['inventory'],
            ...$config['inventory_adjustment'],
            ...$config['invoice_in'],
            ...$config['invoice_out'],
            ...$config['custom_invoice_out'],
            ...$config['custom_invoice_in'],
            ...$config['purchase'],
            ...$config['purchase_item'],
            ...$config['purchase_tax'],
            ...$config['stock_in'],
            ...$config['stock_out'],
            ...$config['stock_movement_in'],
            ...$config['stock_movement_out'],
            ...$config['warehouse'],
            ...$config['business'],
            ...$config['overview'],
            ...$config['extension'],
            ...$config['permission_group'],
            ...$config['permission'],
            ...$config['permission_group_user']
        ];
        $data = $this->hooks->dispatch(
            new HookContext(
                action: HookAction::CREATE,
                phase: HookPhase::RESPONSE,
                timing: HookTiming::BEFORE,
                payload: [
                    ...$data,
                    'permissions' => $permissions,
                    ...$dto->toArray(),
                ],
                module: 'Permission'
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
                    ...$create,
                ],
                module: 'Permission'
            )
        );

        //Event::dispatch('erp.permission.create', [...$data]);
        DB::commit();

        return $data;
    }
}

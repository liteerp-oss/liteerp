<?php

namespace Core\PurchaseTax\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;

use Core\ActivityLog\Application\DTOs\CreateActivityLogRequest;
use Core\ActivityLog\Application\UseCases\CreateActivityLog;
use Core\PurchaseTax\Application\DTOs\CreatePurchaseTaxRequest;
use Core\PurchaseTax\Domain\Services\PurchaseTaxService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreatePurchaseTax
{
    public function __construct(private PurchaseTaxService $service) {}

    public function handle(CreatePurchaseTaxRequest $dto)
    {
        DB::beginTransaction();
        $create = $this->service->create($dto->toArray());
        Event::dispatch(Permission::PURCHASETAX_CREATE->value, [
            ...$create->toArray(),
            'user_id' => $dto->user_id,
            'business_id' => $dto->business_id
        ]);
        DB::commit();
        return $create;
    }
}
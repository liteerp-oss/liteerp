<?php

namespace Core\PurchaseCancel\Application\UseCases;

use Core\PurchaseCancel\Application\DTOs\CreatePurchaseCancelRequest;
use Core\PurchaseCancel\Domain\Services\PurchaseCancelService;

class CreatePurchaseCancel
{
    public function __construct(private PurchaseCancelService $service) {}

    public function handle(array $data)
    {
        $dto = CreatePurchaseCancelRequest::fromArray($data);
        return $this->service->create($dto->toArray());
    }
}
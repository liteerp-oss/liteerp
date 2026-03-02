<?php

namespace Core\PurchaseItem\Application\UseCases;

use App\Exceptions\BadException;
use Core\PurchaseItem\Application\DTOs\CheckForStockMovementInRequest;
use Core\PurchaseItem\Domain\Entities\PurchaseItem;
use Core\PurchaseItem\Domain\Services\PurchaseItemService;

class CheckForStockMovementIn
{
    public function __construct(private PurchaseItemService $service) {}

    public function handle(array $data): PurchaseItem
    {
        $dto = CheckForStockMovementInRequest::fromArray($data);
        $row = $this->service->findById($dto->toArray());
        if ($row->totalQuantity() < $dto->qty_change) {
            throw new BadException(__("purchaseitem::messages.qty_less_than_stock_in"));
        }
        return $row;
    }
}

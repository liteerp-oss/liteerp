<?php

namespace Core\Purchase\Application\UseCases;

use App\Exceptions\BadException;
use Core\Purchase\Domain\Services\PurchaseService;
use Core\Purchase\Domain\Entities\Purchase;

class CheckForPurchaseItem
{
    public function __construct(private PurchaseService $service) {}

    public function handle(array $dto): Purchase
    {
        $row = $this->service->findOneById($dto);
        if(!$row->isDraft()) {
            throw new BadException(__("purchase::messages.cannot_update"));
        }
        return $row;
    }
}

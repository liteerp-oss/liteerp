<?php

namespace Core\Inventory\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\Inventory\Domain\Services\InventoryService;
use Core\Inventory\Domain\Repositories\InventoryRepositoryInterface;
use Core\Inventory\Domain\Entities\Inventory;
use Illuminate\Support\Facades\Log;

class InventoryServiceImpl implements InventoryService
{
    public function __construct(private InventoryRepositoryInterface $repo) {}

}

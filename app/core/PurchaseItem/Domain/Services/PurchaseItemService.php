<?php

namespace Core\PurchaseItem\Domain\Services;

use App\Exceptions\BadException;
use Core\PurchaseItem\Domain\Entities\PurchaseItem;

interface PurchaseItemService
{
    public function create(array $data): PurchaseItem | BadException;
    public function update(array $data): PurchaseItem | BadException;
    public function delete(array $data): PurchaseItem | BadException;
    public function findById(array $data) : PurchaseItem | BadException;
    public function show(array $data) : array | BadException;
    public function indexMinimal(array $data) : array;
}
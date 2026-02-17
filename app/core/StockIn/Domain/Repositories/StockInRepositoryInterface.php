<?php

namespace Core\StockIn\Domain\Repositories;

use Core\StockIn\Domain\Entities\StockIn;

interface StockInRepositoryInterface
{
    public function create(StockIn $entity): StockIn;
    public function findById(array $data) : ?StockIn;
    public function findByInvoiceInId(array $data) : ?StockIn;
    public function show(array $data) : ?array;
    public function update(StockIn $entity): StockIn;
}
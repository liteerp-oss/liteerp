<?php

namespace Core\StockOut\Domain\Repositories;

use App\Exceptions\BadException;
use Core\StockOut\Domain\Entities\StockOut;

interface StockOutRepositoryInterface
{
    public function create(StockOut $entity): StockOut;
    public function findById(array $data) : ?StockOut;
    public function getByInvoiceOutId(array $data) : ?StockOut;
    public function update(StockOut $entity) : StockOut;
    public function findByIdWithFullData(array $data) : array;
}
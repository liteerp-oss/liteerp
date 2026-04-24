<?php

namespace Core\StockOut\Domain\Services;

use App\Exceptions\BadException;
use Core\StockOut\Domain\Entities\StockOut;

interface StockOutService
{
    public function create(array $data): StockOut;
    public function update(array $data) : StockOut | BadException;
    public function show(array $data) : array;
    public function findById(array $data) : StockOut | BadException;
    public function getById(array $data) : ?StockOut;
    public function getByInvoiceOutId(array $data) : ?StockOut;
}
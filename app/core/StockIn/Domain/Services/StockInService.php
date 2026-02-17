<?php

namespace Core\StockIn\Domain\Services;

use App\Exceptions\BadException;
use Core\StockIn\Domain\Entities\StockIn;

interface StockInService
{
    public function create(array $data): StockIn | BadException;
    public function show(array $data) : array | BadException;
    public function update(array $data) : StockIn | BadException;
    public function findById(array $data) : StockIn | BadException;
    public function changeToCancelled(array $data) : StockIn | BadException;
    public function getByInvoiceInId(array $data) : ?StockIn;
}
<?php

namespace Core\OrderShipping\Domain\Services;

use App\Exceptions\BadException;
use Core\OrderShipping\Domain\Entities\OrderShipping;

interface OrderShippingService
{
    public function create(array $data): OrderShipping | BadException;
    public function show(array $data) : array | BadException;
    public function findByOrderId(array $data) : OrderShipping | BadException;
    public function getByOrderId(array $data) : OrderShipping;
    public function findById(array $data) : OrderShipping | BadException;
    public function update(array $data) : OrderShipping | BadException;
}
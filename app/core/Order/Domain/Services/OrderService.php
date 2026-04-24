<?php

namespace Core\Order\Domain\Services;

use App\Exceptions\BadException;
use Core\Order\Domain\Entities\Order;

interface OrderService
{
    public function create(array $data): Order;
    public function show(array $data) : array | BadException;
    public function findOneById(array $data) : Order | BadException;
    public function getOneById(array $data) : ?Order;
    public function update(array $data): Order | BadException;
}
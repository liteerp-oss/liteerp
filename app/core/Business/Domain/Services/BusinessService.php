<?php

namespace Core\Business\Domain\Services;

use App\Exceptions\BadException;
use Core\Business\Domain\Entities\Business;

interface BusinessService
{
    public function create(array $data): Business;
    public function show(array $data): array | BadException;
    public function getById(array $data): ?Business;
    public function update(array $data): Business;
    public function all() : array;
}
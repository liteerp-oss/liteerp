<?php

namespace Core\User\Domain\Services;

use App\Exceptions\BadException;
use Core\User\Domain\Entities\User;

interface UserService
{
    public function findById(array $data): User | BadException;
    public function getByEmail(array $data): ?User;
    public function findByEmailOnSystem(array $data): ?User;
    public function getAll(): array;
}
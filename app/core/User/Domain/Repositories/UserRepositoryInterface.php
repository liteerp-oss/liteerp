<?php

namespace Core\User\Domain\Repositories;

use Core\User\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function findById(array $data): ?User;
    public function findByEmail(array $data): ?User;
    public function findByEmailOnSystem(array $data): ?User;
    public function getAll(): array;
}
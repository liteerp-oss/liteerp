<?php

namespace Core\User\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\User\Domain\Services\UserService;
use Core\User\Domain\Repositories\UserRepositoryInterface;
use Core\User\Domain\Entities\User;
use Illuminate\Support\Facades\Log;

class UserServiceImpl implements UserService
{
    public function __construct(private UserRepositoryInterface $repo) {}

    public function findById(array $data): User|BadException
    {
        return $this->repo->findById($data) ?? throw new BadException(__("user::messages.not_found"));
    }
    public function getByEmail(array $data): ?User
    {
        return $this->repo->findByEmail($data);
    }
    public function findByEmailOnSystem(array $data): ?User
    {
        return $this->repo->findByEmailOnSystem($data);
    }
    public function getAll() : array {
        return $this->repo->getAll();
    }
}

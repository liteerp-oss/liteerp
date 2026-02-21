<?php

namespace Core\User\Application\UseCases;

use Core\User\Domain\Services\UserService;
/**
 * This usecase mean is update user role on business
 * It's not update to account
 */
class GetAllUser
{
    public function __construct(private UserService $service) {}

    public function handle()
    {
        return $this->service->getAll();
    }
}

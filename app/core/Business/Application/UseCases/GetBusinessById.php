<?php

namespace Core\Business\Application\UseCases;
use Core\Business\Domain\Services\BusinessService;

class GetBusinessById
{
    public function __construct(
        private BusinessService $service,
    ) {}
    public function handle(array $data)
    {
        return $this->service->getById($data);
    }
}

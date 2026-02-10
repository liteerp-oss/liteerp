<?php

namespace Core\CustomerGroup\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\CustomerGroup\Domain\Services\CustomerGroupService;
use Core\CustomerGroup\Domain\Repositories\CustomerGroupRepositoryInterface;
use Core\CustomerGroup\Domain\Entities\CustomerGroup;

class CustomerGroupServiceImpl implements CustomerGroupService
{
    public function __construct(private CustomerGroupRepositoryInterface $repo) {}

    public function create(array $data): CustomerGroup
    {

        return $this->repo->create(CustomerGroup::fromArray($data));
    }
    public function index(array $data): array
    {
        return $this->repo->index($data);
    }
    public function show(array $data): CustomerGroup|BadException
    {
        return $this->repo->findById($data) ?? throw new BadException(__("customergroup::messages.not_found"));
    }
    public function update(array $data): CustomerGroup|BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("customergroup::messages.not_found"));
        }
        $entity->name = $data['name'] ?? $entity->name;
        return $this->repo->update($entity);
    }
    public function delete(array $data): CustomerGroup|BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("customergroup::messages.not_found"));
        }
        return $this->repo->delete($entity);
    }
}
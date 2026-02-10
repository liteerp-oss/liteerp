<?php

namespace Core\BusinessRole\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\BusinessRole\Domain\Services\BusinessRoleService;
use Core\BusinessRole\Domain\Repositories\BusinessRoleRepositoryInterface;
use Core\BusinessRole\Domain\Entities\BusinessRole;

class BusinessRoleServiceImpl implements BusinessRoleService
{
    public function __construct(private BusinessRoleRepositoryInterface $repo) {}

    public function create(array $data): BusinessRole
    {
        $entity = BusinessRole::fromArray($data);
        $entity->setDefault();
        return $this->repo->create($entity);
    }
    public function findOne(array $data): BusinessRole | BadException
    {
        return $this->repo->findOne($data) ?? throw new BadException(__("businessrole::messages.not_found"));
    }
    public function listUserByRole(array $data): array {
        return $this->repo->listUserByRole($data);
    }
    public function update(array $data): BusinessRole|BadException
    {
        $entity = $this->repo->findOne($data);
        if(!$entity) {
            throw new BadException(__("businessrole::messages.not_found_role"));
        }
        $entity->role = $data['role'];
        return $this->repo->update($entity);
    }
    public function delete(array $data): BusinessRole|BadException
    {
        $entity = $this->repo->findOne($data);
        if(!$entity) {
            throw new BadException(__("businessrole::messages.not_found_role"));
        }
        return $this->repo->delete($entity);
    }
}
<?php

namespace Core\Warehouse\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\Warehouse\Domain\Services\WarehouseService;
use Core\Warehouse\Domain\Repositories\WarehouseRepositoryInterface;
use Core\Warehouse\Domain\Entities\Warehouse;

class WarehouseServiceImpl implements WarehouseService
{
    public function __construct(private WarehouseRepositoryInterface $repo) {}

    public function create(array $data): Warehouse | BadException
    {

        $entity = Warehouse::fromArray($data);
        if ($this->repo->checkNameExists($entity)) {
            throw new BadException(__("warehouse::messages.name_used"));
        }
        return $this->repo->create($entity);
    }
    public function show(array $data): Warehouse
    {
        $data = $this->repo->findById($data);
        if (!$data) {
            throw new BadException(__("warehouse::messages.not_found"));
        }
        return $data;
    }
    public function update(array $data): Warehouse | BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("warehouse::messages.not_found"));
        }
        if ($entity->name !== $data['name']) {
            if ($this->repo->checkNameExists($entity)) {
                throw new BadException(__("warehouse::messages.name_used"));
            }
        }
        $entity->name = $data['name'];
        $entity->address = $data['address'];
        $entity->active = $data['active'];
        return $this->repo->update($entity);
    }
    public function delete(array $data): Warehouse | BadException
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("warehouse::messages.not_found"));
        }
        return $this->repo->delete($entity);
    }
}

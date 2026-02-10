<?php

namespace Core\Extension\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\Extension\Domain\Services\ExtensionService;
use Core\Extension\Domain\Repositories\ExtensionRepositoryInterface;
use Core\Extension\Domain\Entities\Extension;

class ExtensionServiceImpl implements ExtensionService
{
    public function __construct(private ExtensionRepositoryInterface $repo) {}

    public function create(array $data): Extension | BadException
    {
        $entity = $this->repo->create($data);
            if(!$entity) {
                throw new BadException(__("extension::messages.add_error"));
            }
        return $entity;
    }
    public function update(array $data): Extension | BadException
    {
        $entity = $this->repo->findById($data);
            if(!$entity) {
                throw new BadException(__("extension::messages.not_found"));
            }
        $entity->switchStatus();
            return $this->repo->update($entity) ?? throw new BadException(__("extension::messages.update_error"));
    }
    public function index(array $data): array
    {
        return $this->repo->index($data);
    }
    public function delete(array $data): Extension|BadException
    {
        $entity = $this->repo->findById($data);
            if(!$entity) {
                throw new BadException(__("extension::messages.not_found"));
            }
        return $this->repo->delete($entity);
    }
    public function findById(array $data): Extension|BadException
    {
        $entity = $this->repo->findById($data);
            if(!$entity) {
                throw new BadException(__("extension::messages.not_found"));
            }
        return $entity;
    }
    public function all(): array
    {
        return $this->repo->all();
    }
    public function make(array $data): Extension | BadException
    {
        $entity = $this->repo->make(Extension::fromArray($data));
            if(!$entity) {
                throw new BadException(__("extension::messages.add_error"));
            }
        return $entity;
    }
}
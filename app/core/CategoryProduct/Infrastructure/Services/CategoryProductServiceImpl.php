<?php

namespace Core\CategoryProduct\Infrastructure\Services;

use Illuminate\Support\Str;
use App\Exceptions\BadException;
use Core\CategoryProduct\Domain\Services\CategoryProductService;
use Core\CategoryProduct\Domain\Repositories\CategoryProductRepositoryInterface;
use Core\CategoryProduct\Domain\Entities\CategoryProduct;

class CategoryProductServiceImpl implements CategoryProductService
{
    public function __construct(private CategoryProductRepositoryInterface $repo) {}

    public function create(array $data): CategoryProduct | BadException
    {
        if ($this->repo->checkNameExists($data)) {
            throw new BadException(__("categoryproduct::messages.name_used"));
        }
        $entity = CategoryProduct::fromArray($data);
        return $this->repo->create($entity);
    }
    public function show(array $data): CategoryProduct | BadException
    {
        return $this->repo->findById($data) ?? throw new BadException(__("categoryproduct::messages.not_found"));
    }
    public function update(array $data): CategoryProduct | BadException
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__("categoryproduct::messages.not_found"));
        }
        $exists = $this->repo->checkNameExists($data);
        if ($exists) {
            if ($entity->id !== $exists->id) {
                throw new BadException(__("categoryproduct::messages.name_used"));
            }
        }

        $entity->name = $data['name'];
        $entity->tax = $data['tax'];
        $entity->description = $data['description'] ?? $entity->description;
        return $this->repo->update($entity);
    }
    public function delete(array $data): CategoryProduct|BadException
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__("categoryproduct::messages.not_found"));
        }
        return $this->repo->delete($entity);
    }
}

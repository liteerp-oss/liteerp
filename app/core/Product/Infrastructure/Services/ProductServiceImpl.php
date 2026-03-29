<?php

namespace Core\Product\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\Product\Domain\Services\ProductService;
use Core\Product\Domain\Repositories\ProductRepositoryInterface;
use Core\Product\Domain\Entities\Product;

class ProductServiceImpl implements ProductService
{
    public function __construct(private ProductRepositoryInterface $repo) {}

    public function create(array $data): Product
    {
        $entity = Product::fromArray($data);
        if ($this->repo->checkExists($data)) {
            throw new BadException(__("product::messages.sku_used"));
        }
        return $this->repo->create($entity);
    }
    public function show(array $data): array | BadException
    {
        return $this->repo->findOneWithFullData($data) ?? throw new BadException(__("product::messages.not_found"));
    }
    public function findById(array $data): Product|BadException
    {
        return $this->repo->findById($data) ?? throw new BadException(__("product::messages.not_found"));
    }
    public function update(array $data): Product
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__("product::messages.not_found_for_update"));
        }
        $check = $this->repo->checkExists($data);
        if ($check) {
            if($check->id !== $entity->id) {
                throw new BadException(__("product::messages.sku_used"));
            }
        }
        $entity->description = $data['description'];
        $entity->image     = $data['image'] ?? null;
        $entity->category_id     = $data['category_id'] ?? null;
        $entity->unit     = $data['unit'] ?? null;
        $entity->name     = $data['name'] ?? null;
        $entity->sku     = $data['sku'] ?? null;
        return $this->repo->update($entity);
    }
    public function delete(array $data): Product
    {
        $entity = $this->repo->findById($data);
        if (!$entity) {
            throw new BadException(__("product::messages.not_found_for_update"));
        }
        return $this->repo->delete($entity);
    }
}

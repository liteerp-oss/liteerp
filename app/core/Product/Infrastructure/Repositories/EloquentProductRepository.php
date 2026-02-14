<?php

namespace Core\Product\Infrastructure\Repositories;

use App\Models\ProductModel;
use Core\Product\Domain\Repositories\ProductRepositoryInterface;
use Core\Product\Domain\Entities\Product;
use Illuminate\Support\Facades\Log;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function create(Product $entity): Product
    {
        $create = ProductModel::create($entity->toArray());
        $entity->id = $create['id'];
        return $entity;
    }
    public function checkExists(array $data): ?Product
    {
        $row = ProductModel::where('sku', $data['sku'])
        ->where('business_id',$data['business_id'])?->first();
        if(!$row) {
            return null;
        }
        return Product::fromArray($row->toArray());
    }
    public function findOneWithFullData(array $data): ?array
    {
        return ProductModel::with(['category'])->where('id', $data['id'])
            ->where('business_id', $data['business_id'])->first()?->toArray();
    }
    public function findById(array $data): ?Product
    {
        $row = ProductModel::where('id', $data['id'])
            ->where('business_id', $data['business_id']);
        if ($row->count() == false) {
            return null;
        }
        $row = $row->first();
        $entity = Product::fromArray($row->toArray());
        $entity->id = $row->id;
        return $entity;
    }
    public function update(Product $entity): Product
    {
        ProductModel::where('id', $entity->id)
        ->where('business_id', $entity->business_id)->update($entity->toArray());
        return $entity;
    }
    public function delete(Product $entity): Product
    {
        ProductModel::where('id', $entity->id)
        ->where('business_id', $entity->business_id)
        ->delete();
        return $entity;
    }
}

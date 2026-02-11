<?php

namespace Core\PriceList\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\PriceList\Domain\Services\PriceListService;
use Core\PriceList\Domain\Repositories\PriceListRepositoryInterface;
use Core\PriceList\Domain\Entities\PriceList;

class PriceListServiceImpl implements PriceListService
{
    public function __construct(private PriceListRepositoryInterface $repo) {}

    public function create(array $data): PriceList
    {
        $entity = $this->repo->findByProductAndGroup($data);
          if($entity) {
              throw new BadException(__("pricelist::messages.product_used_in_group")); 
          }
        $entity = PriceList::fromArray($data);
        return $this->repo->create($entity);
    }
    public function update(array $data): PriceList|BadException
    {
        $entity = $this->repo->findByProductAndGroup($data);
            if(!$entity) {
                throw new BadException(__("pricelist::messages.not_found"));
            }
        return $this->repo->update($entity);
    }
    public function findByProductAndGroup(array $data): PriceList|BadException
    {
        return $this->repo->findByProductAndGroup($data) 
              ?? throw new BadException(__("pricelist::messages.not_found"));
    }
    public function index(array $data): array
    {
        return $this->repo->index($data);
    }
    public function delete(array $data): PriceList|BadException
    {
        $entity = $this->repo->findById($data);
            if(!$entity) {
                throw new BadException(__("pricelist::messages.not_found"));
            }
        return $this->repo->delete($entity);
    }
}
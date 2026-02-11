<?php

namespace Core\StockMovementOut\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\StockMovementOut\Domain\Services\StockMovementOutService;
use Core\StockMovementOut\Domain\Repositories\StockMovementOutRepositoryInterface;
use Core\StockMovementOut\Domain\Entities\StockMovementOut;

class StockMovementOutServiceImpl implements StockMovementOutService
{
    public function __construct(private StockMovementOutRepositoryInterface $repo) {}

    public function create(array $data): StockMovementOut
    {
        if($this->repo->findExists($data)) {
            throw new BadException(__("stockmovementout::messages.product_used_in_order"));
        }
        $entity = StockMovementOut::fromArray($data);
        return $this->repo->create($entity);
    }
    public function show(array $data): StockMovementOut
    {
        throw new \Exception('Not implemented');
    }
    public function index(array $data): array
    {
        return $this->repo->index($data);
    }
    public function indexWithLimit(array $data): array
    {
        return $this->repo->indexWithLimit($data);
    }
    public function update(array $data): StockMovementOut
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("stockmovementout::messages.not_found"));
        }
        //$entity->qty_change = $data['qty_change'];
        return $this->repo->update($entity);
    }
    public function findById(array $data): StockMovementOut
    {
        return $this->repo->findById($data);
    }
}

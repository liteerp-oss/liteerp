<?php

namespace Core\StockMovementIn\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\StockMovementIn\Domain\Services\StockMovementInService;
use Core\StockMovementIn\Domain\Repositories\StockMovementInRepositoryInterface;
use Core\StockMovementIn\Domain\Entities\StockMovementIn;

class StockMovementInServiceImpl implements StockMovementInService
{
    public function __construct(private StockMovementInRepositoryInterface $repo) {}

    public function create(array $data): StockMovementIn
    {
        $entity = $this->repo->checkExists($data);
        if($entity) {
            throw new BadException(__("stockmovementin::messages.already_exists"));
        }
        $entity = StockMovementIn::fromArray($data);

        return $this->repo->create($entity);
    }
    public function index(array $data): array
    {
        return $this->repo->index($data);
    }
    public function update(array $data): StockMovementIn
    {
        $entity = $this->repo->findById($data);
        if(!$entity) {
            throw new BadException(__("stockmovementin::messages.not_found"));
        }
        $entity->qty_change = $data['qty_change'];
        return $this->repo->update($entity);
    }
}

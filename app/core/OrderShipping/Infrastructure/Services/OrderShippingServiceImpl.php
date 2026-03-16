<?php

namespace Core\OrderShipping\Infrastructure\Services;

use App\Exceptions\BadException;
use Core\OrderShipping\Domain\Services\OrderShippingService;
use Core\OrderShipping\Domain\Repositories\OrderShippingRepositoryInterface;
use Core\OrderShipping\Domain\Entities\OrderShipping;
use Illuminate\Support\Facades\Log;

class OrderShippingServiceImpl implements OrderShippingService
{
    public function __construct(private OrderShippingRepositoryInterface $repo) {}

    public function create(array $data): OrderShipping | BadException
    {
        $row = $this->repo->findByOrderId($data);
        if($row) {
              throw new BadException(__("ordershipping::messages.used"));
        }
        $entity = OrderShipping::fromArray($data);
        return $this->repo->create($entity);
    }
    public function show(array $data): array | BadException
    {
        return $this->repo->findByIdWithFullData($data) ?? throw new BadException(__("Not found data"));
    }
    public function update(array $data): OrderShipping|BadException
    {
        $entity = $this->repo->findByOrderId($data);
        if(!$entity) {
              throw new BadException(__("ordershipping::messages.not_found"));
        }
        $entity->receiver_name = $data['receiver_name'] ?? $entity->receiver_name;
        $entity->receiver_phone = $data['receiver_phone'] ?? $entity->receiver_phone;
        $entity->receiver_address = $data['receiver_address'] ?? $entity->receiver_address;
        $entity->receiver_note = $data['receiver_note'] ?? $entity->receiver_note;
        $entity->preferred_unit  = $data['preferred_unit'] ?? $entity->preferred_unit;
        $entity->shipping_fee_actual = $data['shipping_fee_actual'] ?? $entity->shipping_fee_actual;
        $entity->shipping_code = $data['shipping_code'] ?? $entity->shipping_code;
        $entity->shipped_at = $data['shipped_at'] ?? $entity->shipped_at;
        $entity->delivered_at = $data['delivered_at'] ?? $entity->delivered_at;
        $entity->shipping_fee_estimated = $data['shipping_fee_estimated'] ?? $entity->shipping_fee_estimated;
        $entity->isFeeActualApplied();
        return $this->repo->update($entity);
    }
    public function findByOrderId(array $data): OrderShipping|BadException
    {
        return $this->repo->findByOrderId($data) 
              ?? throw new BadException(__("ordershipping::messages.not_found"));
    }
    public function getByOrderId(array $data): OrderShipping
    {
        return $this->repo->findByOrderId($data);
    }
    public function findById(array $data): OrderShipping|BadException
    {
        return $this->repo->findById($data) 
              ?? throw new BadException(__("ordershipping::messages.not_found"));
    }
}
<?php

namespace Core\OrderItem\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;


use Core\OrderItem\Application\DTOs\CancelledOrderItemRequest;
use Core\OrderItem\Domain\Services\OrderItemService;
use Illuminate\Support\Facades\Event;

class CancelledOrderItem
{
    public function __construct(private OrderItemService $service) {}

    public function handle(array $data)
    {
        $dto = CancelledOrderItemRequest::fromArray($data);
        return $this->service->cancelByOrderItem($dto->toArray());
    }
}
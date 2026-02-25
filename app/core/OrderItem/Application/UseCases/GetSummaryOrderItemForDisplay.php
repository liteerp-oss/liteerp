<?php

namespace Core\OrderItem\Application\UseCases;

use Core\OrderItem\Domain\Services\OrderItemService;
use Core\Order\Application\UseCases\FindOneById;
use Core\OrderItem\Application\DTOs\GetSummaryOrderItemRequest;
use Illuminate\Support\Facades\Event;

class GetSummaryOrderItemForDisplay
{
    public function __construct(private OrderItemService $service) {}

    public function handle(array $data)
    {
        $dto = GetSummaryOrderItemRequest::fromArray($data);
        $summary = $this->service->summary($dto->toArray());
        return $summary;
    }
}

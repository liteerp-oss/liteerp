<?php

namespace Core\OrderItem\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;
use Core\OrderItem\Domain\Services\OrderItemService;
use Core\Order\Application\UseCases\FindOneById;
use Core\OrderItem\Application\DTOs\GetSummaryOrderItemRequest;
use Illuminate\Support\Facades\Event;

class GetSummaryOrderItem
{
    public function __construct(private OrderItemService $service) {}

    public function handle(array $data)
    {
        $dto = GetSummaryOrderItemRequest::fromArray($data);
        $summary = $this->service->summary($dto->toArray());
        Event::dispatch(Permission::ORDERITEM_SUMMARY->value,[
            ...$data,
            ...$dto->toArray(),
            ...$summary,
            'id' => $dto->order_id,
        ]);
    }
}

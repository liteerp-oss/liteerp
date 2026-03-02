<?php

namespace Core\Customer\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;
use Core\Customer\Application\DTOs\OrderShippingCustomerRequest;
use Core\Customer\Domain\Services\CustomerService;
use Illuminate\Support\Facades\Event;

class OrderShippingCustomer
{
    public function __construct(private CustomerService $service) {}

    public function handle(OrderShippingCustomerRequest $dto)
    {
        $customer = $this->service->show($dto->toArray());

        Event::dispatch(Permission::CUSTOMER_CREATORDEALERSHIPPING->value, [
            'order_id' => $dto->order_id,
            'receiver_name' => $customer->name,
            'receiver_phone' => $customer->phone,
            'receiver_address' => $customer->address,
            'business_id' => $dto->business_id,
            'user_id' => $dto->created_by,
            ...$customer->toArray()
        ]);
    }
}

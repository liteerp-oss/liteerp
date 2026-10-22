<?php

namespace Tests\Unit;

use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\Customer\Application\UseCases\CreateCustomer;
use Core\Customer\Domain\Entities\Customer;
use Core\Customer\Domain\Services\CustomerService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateCustomerTest extends TestCase
{
    protected $serviceMock;
    protected $hooksMock;
    protected $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        DB::shouldReceive('beginTransaction')->andReturn(null);
        DB::shouldReceive('commit')->andReturn(null);

        $this->serviceMock = Mockery::mock(CustomerService::class);
        $this->hooksMock = Mockery::mock(HookDispatcher::class);
        $this->useCase = new CreateCustomer($this->serviceMock, $this->hooksMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_customer_successfully()
    {
        $data = [
            'business_id' => 123,
            'name' => 'Test Customer',
            'phone' => '1234567890',
            'group' => 1,
            'created_by' => 1,
            'user_id' => 1,
        ];

        $customer = Customer::fromArray([
            'business_id' => 123,
            'name' => 'Test Customer',
            'phone' => '1234567890',
            'group' => 1,
        ]);
        $customer->id = 1;

        // Mock hooks dispatch - return input data for first call, customer array for second, customer array for third
        $this->hooksMock->shouldReceive('dispatch')
            ->andReturn($data, $customer->toArray(), $customer->toArray());

        $this->serviceMock->shouldReceive('create')->andReturn($customer);

        Event::shouldReceive('dispatch')
            ->with('erp.customer.create', Mockery::type('array'))
            ->once();

        Event::shouldReceive('dispatch')
            ->with('erp.activitylog.create', Mockery::on(function ($payload) use ($customer, $data) {
                return $payload === [
                    'user_id' => $data['user_id'],
                    'business_id' => $customer->business_id,
                    'type' => 'customer',
                    'id' => $customer->id,
                    'data' => $customer->toArray(),
                ];
            }))
            ->once();

        $result = $this->useCase->handle($data);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
    }
}
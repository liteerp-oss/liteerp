<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use Core\Customer\Domain\Entities\Customer;
use Core\Customer\Domain\Repositories\CustomerRepositoryInterface;
use Core\Customer\Infrastructure\Services\CustomerServiceImpl;
use Tests\TestCase;
use Mockery;

class CustomerServiceImplTest extends TestCase
{
    protected $repoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoMock = Mockery::mock(CustomerRepositoryInterface::class);
        $this->service = new CustomerServiceImpl($this->repoMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_returns_customer_when_phone_not_used()
    {
        $data = [
            'business_id' => 123,
            'name' => 'Test Customer',
            'phone' => '1234567890',
            'group' => 1,
        ];

        $customer = Customer::fromArray([
            'business_id' => 123,
            'name' => 'Test Customer',
            'phone' => '1234567890',
            'group' => 1,
        ]);

        $this->repoMock->shouldReceive('findByPhone')->with($data)->andReturn(null);
        $this->repoMock->shouldReceive('create')->andReturn($customer);

        $result = $this->service->create($data);

        $this->assertInstanceOf(Customer::class, $result);
    }

    public function test_create_throws_exception_when_phone_already_used()
    {
        $data = [
            'business_id' => 123,
            'name' => 'Test Customer',
            'phone' => '1234567890',
            'group' => 1,
        ];

        $existingCustomer = Customer::fromArray([
            'business_id' => 123,
            'name' => 'Existing Customer',
            'phone' => '1234567890',
            'group' => 1,
        ]);

        $this->repoMock->shouldReceive('findByPhone')->with($data)->andReturn($existingCustomer);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('customer::messages.phone_used'));

        $this->service->create($data);
    }

    public function test_update_returns_updated_customer()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'name' => 'Updated Customer',
            'phone' => '1234567890',
            'group' => 1,
        ];

        $existingCustomer = Customer::fromArray([
            'business_id' => 123,
            'name' => 'Old Customer',
            'phone' => '0987654321',
            'group' => 1,
        ]);
        $existingCustomer->id = 1;

        $updatedCustomer = Customer::fromArray([
            'business_id' => 123,
            'name' => 'Updated Customer',
            'phone' => '1234567890',
            'group' => 1,
        ]);
        $updatedCustomer->id = 1;

        $this->repoMock->shouldReceive('findByPhone')->with($data)->andReturn(null);
        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($existingCustomer);
        $this->repoMock->shouldReceive('update')->andReturn($updatedCustomer);

        $result = $this->service->update($data);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals('Updated Customer', $result->name);
        $this->assertEquals('1234567890', $result->phone);
    }

    public function test_update_throws_exception_when_phone_already_used_by_another_customer()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'name' => 'Updated Customer',
            'phone' => '1234567890',
            'group' => 1,
        ];

        $anotherCustomer = Customer::fromArray([
            'business_id' => 123,
            'name' => 'Another Customer',
            'phone' => '1234567890',
            'group' => 1,
        ]);
        $anotherCustomer->id = 2;

        $this->repoMock->shouldReceive('findByPhone')->with($data)->andReturn($anotherCustomer);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('customer::messages.phone_used'));

        $this->service->update($data);
    }

    public function test_update_throws_exception_when_customer_not_found()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'name' => 'Updated Customer',
            'group' => 1,
        ];

        $this->repoMock->shouldReceive('findByPhone')->with($data)->andReturn(null);
        $this->repoMock->shouldReceive('findById')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('customer::messages.not_found'));

        $this->service->update($data);
    }

    public function test_show_returns_customer()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $customer = Customer::fromArray([
            'business_id' => 123,
            'name' => 'Test Customer',
            'group' => 1
        ]);
        $customer->id = 1;

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($customer);

        $result = $this->service->show($data);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_show_throws_exception_when_customer_not_found()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('customer::messages.not_found'));

        $this->service->show($data);
    }

    public function test_delete_returns_deleted_customer()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $customer = Customer::fromArray([
            'business_id' => 123,
            'name' => 'Test Customer',
            'group' => 1
        ]);
        $customer->id = 1;

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($customer);
        $this->repoMock->shouldReceive('delete')->andReturn($customer);

        $result = $this->service->delete($data);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_delete_throws_exception_when_customer_not_found()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('customer::messages.not_found'));

        $this->service->delete($data);
    }
}
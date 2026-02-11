<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use Core\CustomerGroup\Domain\Entities\CustomerGroup;
use Core\CustomerGroup\Domain\Repositories\CustomerGroupRepositoryInterface;
use Core\CustomerGroup\Infrastructure\Services\CustomerGroupServiceImpl;
use Tests\TestCase;
use Mockery;

class CustomerGroupServiceImplTest extends TestCase
{
    protected $repoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoMock = Mockery::mock(CustomerGroupRepositoryInterface::class);
        $this->service = new CustomerGroupServiceImpl($this->repoMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_returns_customer_group()
    {
        $data = [
            'business_id' => 1,
            'name' => 'VIP Customers'
        ];

        $customerGroup = CustomerGroup::fromArray($data);
        $customerGroup->id = 1;

        $this->repoMock->shouldReceive('create')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof CustomerGroup &&
                       $arg->business_id === 1 &&
                       $arg->name === 'VIP Customers';
            }))
            ->andReturn($customerGroup);

        $result = $this->service->create($data);

        $this->assertInstanceOf(CustomerGroup::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('VIP Customers', $result->name);
    }

    public function test_index_returns_array()
    {
        $data = ['business_id' => 1];
        $expectedResult = [
            ['id' => 1, 'name' => 'VIP Customers', 'business_id' => 1],
            ['id' => 2, 'name' => 'Regular Customers', 'business_id' => 1]
        ];

        $this->repoMock->shouldReceive('index')
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->service->index($data);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('VIP Customers', $result[0]['name']);
    }

    public function test_show_returns_customer_group()
    {
        $data = ['id' => 1];
        $customerGroup = CustomerGroup::fromArray(['id' => 1, 'business_id' => 1, 'name' => 'VIP Customers']);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($customerGroup);

        $result = $this->service->show($data);

        $this->assertInstanceOf(CustomerGroup::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('VIP Customers', $result->name);
    }

    public function test_show_throws_exception_when_customer_group_not_found()
    {
        $data = ['id' => 1];

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('customergroup::messages.not_found'));

        $this->service->show($data);
    }

    public function test_update_returns_updated_customer_group()
    {
        $data = [
            'id' => 1,
            'name' => 'Updated Customer Group'
        ];

        $existingCustomerGroup = CustomerGroup::fromArray(['id' => 1, 'business_id' => 1, 'name' => 'Old Name']);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($existingCustomerGroup);

        $updatedCustomerGroup = CustomerGroup::fromArray(['id' => 1, 'business_id' => 1, 'name' => 'Updated Customer Group']);

        $this->repoMock->shouldReceive('update')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof CustomerGroup &&
                       $arg->name === 'Updated Customer Group';
            }))
            ->andReturn($updatedCustomerGroup);

        $result = $this->service->update($data);

        $this->assertInstanceOf(CustomerGroup::class, $result);
        $this->assertEquals('Updated Customer Group', $result->name);
    }

    public function test_update_throws_exception_when_customer_group_not_found()
    {
        $data = [
            'id' => 1,
            'name' => 'Updated Customer Group'
        ];

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('customergroup::messages.not_found'));

        $this->service->update($data);
    }

    public function test_delete_returns_deleted_customer_group()
    {
        $data = ['id' => 1];
        $customerGroup = CustomerGroup::fromArray(['id' => 1, 'business_id' => 1, 'name' => 'VIP Customers']);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($customerGroup);

        $this->repoMock->shouldReceive('delete')
            ->with($customerGroup)
            ->andReturn($customerGroup);

        $result = $this->service->delete($data);

        $this->assertInstanceOf(CustomerGroup::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_delete_throws_exception_when_customer_group_not_found()
    {
        $data = ['id' => 1];

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('customergroup::messages.not_found'));

        $this->service->delete($data);
    }
}
<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use Core\Warehouse\Domain\Entities\Warehouse;
use Core\Warehouse\Domain\Repositories\WarehouseRepositoryInterface;
use Core\Warehouse\Infrastructure\Services\WarehouseServiceImpl;
use Tests\TestCase;
use Mockery;

class WarehouseServiceImplTest extends TestCase
{
    protected $repoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoMock = Mockery::mock(WarehouseRepositoryInterface::class);
        $this->service = new WarehouseServiceImpl($this->repoMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_returns_warehouse_when_name_not_used()
    {
        $data = [
            'name' => 'Main Warehouse',
            'address' => '123 Main St',
            'business_id' => 123,
            'created_by' => 1,
        ];

        $warehouse = Warehouse::fromArray([
            'name' => 'Main Warehouse',
            'address' => '123 Main St',
            'business_id' => 123,
            'created_by' => 1,
        ]);
        $warehouse->id = 1;

        $this->repoMock->shouldReceive('checkNameExists')->andReturn(false);
        $this->repoMock->shouldReceive('create')->andReturn($warehouse);

        $result = $this->service->create($data);

        $this->assertInstanceOf(Warehouse::class, $result);
    }

    public function test_create_throws_exception_when_name_already_used()
    {
        $data = [
            'name' => 'Main Warehouse',
            'address' => '123 Main St',
            'business_id' => 123,
            'created_by' => 1,
        ];

        $this->repoMock->shouldReceive('checkNameExists')->andReturn(true);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('warehouse::messages.name_used'));

        $this->service->create($data);
    }

    public function test_show_returns_warehouse()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $warehouse = Warehouse::fromArray([
            'name' => 'Main Warehouse',
            'address' => '123 Main St',
            'business_id' => 123,
            'created_by' => 1
        ]);
        $warehouse->id = 1;

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($warehouse);

        $result = $this->service->show($data);

        $this->assertInstanceOf(Warehouse::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_show_throws_exception_when_warehouse_not_found()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('warehouse::messages.not_found'));

        $this->service->show($data);
    }

    public function test_update_returns_updated_warehouse()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'name' => 'Updated Warehouse',
            'address' => 'Updated Address',
            'active' => false,
        ];

        $existingWarehouse = Warehouse::fromArray([
            'name' => 'Old Warehouse',
            'address' => 'Old Address',
            'business_id' => 123,
            'created_by' => 1
        ]);
        $existingWarehouse->id = 1;

        $updatedWarehouse = Warehouse::fromArray([
            'name' => 'Updated Warehouse',
            'address' => 'Updated Address',
            'business_id' => 123,
            'active' => false,
            'created_by' => 1
        ]);
        $updatedWarehouse->id = 1;

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($existingWarehouse);
        $this->repoMock->shouldReceive('checkNameExists')->andReturn(false);
        $this->repoMock->shouldReceive('update')->andReturn($updatedWarehouse);

        $result = $this->service->update($data);

        $this->assertInstanceOf(Warehouse::class, $result);
        $this->assertEquals('Updated Warehouse', $result->name);
        $this->assertEquals('Updated Address', $result->address);
        $this->assertFalse($result->active);
    }

    public function test_update_throws_exception_when_warehouse_not_found()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'name' => 'Updated Warehouse',
            'address' => 'Updated Address',
            'active' => false,
        ];

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('warehouse::messages.not_found'));

        $this->service->update($data);
    }

    public function test_update_throws_exception_when_name_already_used()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'name' => 'Updated Warehouse',
            'address' => 'Updated Address',
            'active' => false,
        ];

        $existingWarehouse = Warehouse::fromArray([
            'name' => 'Old Warehouse',
            'address' => 'Old Address',
            'business_id' => 123,
            'created_by' => 1
        ]);
        $existingWarehouse->id = 1;

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($existingWarehouse);
        $this->repoMock->shouldReceive('checkNameExists')->andReturn(true);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('warehouse::messages.name_used'));

        $this->service->update($data);
    }

    public function test_delete_returns_deleted_warehouse()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $warehouse = Warehouse::fromArray([
            'name' => 'Main Warehouse',
            'address' => '123 Main St',
            'business_id' => 123,
            'created_by' => 1
        ]);
        $warehouse->id = 1;

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($warehouse);
        $this->repoMock->shouldReceive('delete')->andReturn($warehouse);

        $result = $this->service->delete($data);

        $this->assertInstanceOf(Warehouse::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_delete_throws_exception_when_warehouse_not_found()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('warehouse::messages.not_found'));

        $this->service->delete($data);
    }
}

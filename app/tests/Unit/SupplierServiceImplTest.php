<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use Core\Supplier\Domain\Entities\Supplier;
use Core\Supplier\Domain\Repositories\SupplierRepositoryInterface;
use Core\Supplier\Infrastructure\Services\SupplierServiceImpl;
use Tests\TestCase;
use Mockery;

class SupplierServiceImplTest extends TestCase
{
    protected $repoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoMock = Mockery::mock(SupplierRepositoryInterface::class);
        $this->service = new SupplierServiceImpl($this->repoMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_returns_supplier_when_name_not_used()
    {
        $data = [
            'business_id' => 123,
            'unit_name' => 'Test Supplier',
            'email' => 'supplier@example.com',
            'phone' => '1234567890',
        ];

        $supplier = Supplier::fromArray($data);
        $supplier->id = 1;

        $this->repoMock->shouldReceive('findByName')->with($data)->andReturn(null);
        $this->repoMock->shouldReceive('create')->andReturn($supplier);

        $result = $this->service->create($data);

        $this->assertInstanceOf(Supplier::class, $result);
    }

    public function test_create_throws_exception_when_name_already_used()
    {
        $data = [
            'business_id' => 123,
            'unit_name' => 'Test Supplier',
            'email' => 'supplier@example.com',
            'phone' => '1234567890',
        ];

        $existingSupplier = Supplier::fromArray($data);
        $existingSupplier->id = 2;

        $this->repoMock->shouldReceive('findByName')->with($data)->andReturn($existingSupplier);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('supplier::messages.name_used'));

        $this->service->create($data);
    }

    public function test_update_returns_updated_supplier()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'unit_name' => 'Updated Supplier',
            'email' => 'updated@example.com',
            'phone' => '0987654321',
            'address' => 'Updated Address',
            'tax_code' => '987654321',
            'bank_name' => 'Updated Bank',
            'bank_account' => '0987654321',
            'website' => 'https://updated.com',
            'note' => 'Updated note',
            'active' => true,
        ];

        $existingSupplier = Supplier::fromArray([
            'business_id' => 123,
            'unit_name' => 'Old Supplier',
            'email' => 'old@example.com',
            'phone' => '1234567890',
        ]);
        $existingSupplier->id = 1;

        $updatedSupplier = Supplier::fromArray([
            'business_id' => 123,
            'unit_name' => 'Updated Supplier',
            'email' => 'updated@example.com',
            'phone' => '0987654321',
            'address' => 'Updated Address',
            'tax_code' => '987654321',
            'bank_name' => 'Updated Bank',
            'bank_account' => '0987654321',
            'website' => 'https://updated.com',
            'note' => 'Updated note',
            'active' => true,
        ]);
        $updatedSupplier->id = 1;

        $this->repoMock->shouldReceive('findByName')->with($data)->andReturn(null);
        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($existingSupplier);
        $this->repoMock->shouldReceive('update')->andReturn($updatedSupplier);

        $result = $this->service->update($data);

        $this->assertInstanceOf(Supplier::class, $result);
        $this->assertEquals('Updated Supplier', $result->unit_name);
        $this->assertEquals('updated@example.com', $result->email);
        $this->assertEquals('0987654321', $result->phone);
        $this->assertEquals('Updated Address', $result->address);
        $this->assertEquals('987654321', $result->tax_code);
        $this->assertEquals('Updated Bank', $result->bank_name);
        $this->assertEquals('0987654321', $result->bank_account);
        $this->assertEquals('https://updated.com', $result->website);
        $this->assertEquals('Updated note', $result->note);
        $this->assertTrue($result->active);
    }

    public function test_update_throws_exception_when_name_already_used_by_another_supplier()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'unit_name' => 'Updated Supplier',
            'email' => 'updated@example.com',
        ];

        $anotherSupplier = Supplier::fromArray([
            'business_id' => 123,
            'unit_name' => 'Updated Supplier',
            'email' => 'another@example.com',
        ]);
        $anotherSupplier->id = 2;

        $this->repoMock->shouldReceive('findByName')->with($data)->andReturn($anotherSupplier);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('supplier::messages.name_used'));

        $this->service->update($data);
    }

    public function test_update_throws_exception_when_supplier_not_found()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'unit_name' => 'Updated Supplier',
        ];

        $this->repoMock->shouldReceive('findByName')->with($data)->andReturn(null);
        $this->repoMock->shouldReceive('findById')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('supplier::messages.not_found'));

        $this->service->update($data);
    }

    public function test_delete_returns_deleted_supplier()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $supplier = Supplier::fromArray([
            'business_id' => 123,
            'unit_name' => 'Test Supplier',
            'email' => 'supplier@example.com',
        ]);
        $supplier->id = 1;

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($supplier);
        $this->repoMock->shouldReceive('delete')->andReturn($supplier);

        $result = $this->service->delete($data);

        $this->assertInstanceOf(Supplier::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_delete_throws_exception_when_supplier_not_found()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('supplier::messages.not_found'));

        $this->service->delete($data);
    }
}
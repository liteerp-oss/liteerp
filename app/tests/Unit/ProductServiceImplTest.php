<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use Core\Product\Domain\Entities\Product;
use Core\Product\Domain\Repositories\ProductRepositoryInterface;
use Core\Product\Infrastructure\Services\ProductServiceImpl;
use Tests\TestCase;
use Mockery;

class ProductServiceImplTest extends TestCase
{
    protected $repoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoMock = Mockery::mock(ProductRepositoryInterface::class);
        $this->service = new ProductServiceImpl($this->repoMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_returns_product_when_sku_not_used()
    {
        $data = [
            'business_id' => 123,
            'category_id' => 456,
            'sku' => 'PROD-001',
            'name' => 'Test Product',
            'unit' => 'pcs',
            'created_by' => 1,
        ];

        $product = Product::fromArray($data);
        $product->id = 1;

        $this->repoMock->shouldReceive('checkExists')->with($data)->andReturn(null);
        $this->repoMock->shouldReceive('create')->andReturn($product);

        $result = $this->service->create($data);

        $this->assertInstanceOf(Product::class, $result);
    }

    public function test_create_throws_exception_when_sku_already_used()
    {
        $data = [
            'business_id' => 123,
            'category_id' => 456,
            'sku' => 'PROD-001',
            'name' => 'Test Product',
            'unit' => 'pcs',
            'created_by' => 1,
        ];

        $existingProduct = Product::fromArray($data);
        $existingProduct->id = 2;

        $this->repoMock->shouldReceive('checkExists')->with($data)->andReturn($existingProduct);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('product::messages.sku_used'));

        $this->service->create($data);
    }

    public function test_show_returns_product_data()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $productData = [
            'id' => 1,
            'business_id' => 123,
            'category_id' => 456,
            'sku' => 'PROD-001',
            'name' => 'Test Product',
            'unit' => 'pcs',
            'created_by' => 1,
        ];

        $this->repoMock->shouldReceive('findOneWithFullData')->with($data)->andReturn($productData);

        $result = $this->service->show($data);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Test Product', $result['name']);
    }

    public function test_show_throws_exception_when_product_not_found()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $this->repoMock->shouldReceive('findOneWithFullData')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('product::messages.not_found'));

        $this->service->show($data);
    }

    public function test_update_returns_updated_product()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'category_id' => 456,
            'sku' => 'PROD-001-UPDATED',
            'name' => 'Updated Product',
            'unit' => 'kg',
            'description' => 'Updated description',
            'image' => 'updated.jpg',
        ];

        $existingProduct = Product::fromArray([
            'business_id' => 123,
            'category_id' => 456,
            'sku' => 'PROD-001',
            'name' => 'Old Product',
            'unit' => 'pcs',
            'created_by' => 1,
        ]);
        $existingProduct->id = 1;

        $updatedProduct = Product::fromArray([
            'business_id' => 123,
            'category_id' => 456,
            'sku' => 'PROD-001-UPDATED',
            'name' => 'Updated Product',
            'unit' => 'kg',
            'description' => 'Updated description',
            'image' => 'updated.jpg',
            'created_by' => 1,
        ]);
        $updatedProduct->id = 1;

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($existingProduct);
        $this->repoMock->shouldReceive('checkExists')->with($data)->andReturn(null);
        $this->repoMock->shouldReceive('update')->andReturn($updatedProduct);

        $result = $this->service->update($data);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals('Updated Product', $result->name);
        $this->assertEquals('PROD-001-UPDATED', $result->sku);
        $this->assertEquals('kg', $result->unit);
        $this->assertEquals('Updated description', $result->description);
        $this->assertEquals('updated.jpg', $result->image);
    }

    public function test_update_throws_exception_when_product_not_found()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'sku' => 'PROD-001',
            'name' => 'Updated Product',
            'unit' => 'kg',
        ];

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('product::messages.not_found_for_update'));

        $this->service->update($data);
    }

    public function test_update_throws_exception_when_sku_already_used_by_another_product()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
            'sku' => 'PROD-001',
            'name' => 'Updated Product',
            'unit' => 'kg',
        ];

        $existingProduct = Product::fromArray([
            'business_id' => 123,
            'category_id' => 456,
            'sku' => 'PROD-001',
            'name' => 'Old Product',
            'unit' => 'pcs',
            'created_by' => 1,
        ]);
        $existingProduct->id = 1;

        $anotherProduct = Product::fromArray([
            'business_id' => 123,
            'category_id' => 456,
            'sku' => 'PROD-001',
            'name' => 'Another Product',
            'unit' => 'pcs',
            'created_by' => 1,
        ]);
        $anotherProduct->id = 2;

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($existingProduct);
        $this->repoMock->shouldReceive('checkExists')->with($data)->andReturn($anotherProduct);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('product::messages.sku_used'));

        $this->service->update($data);
    }

    public function test_delete_returns_deleted_product()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $product = Product::fromArray([
            'business_id' => 123,
            'category_id' => 456,
            'sku' => 'PROD-001',
            'name' => 'Test Product',
            'unit' => 'pcs',
            'created_by' => 1,
        ]);
        $product->id = 1;

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn($product);
        $this->repoMock->shouldReceive('delete')->andReturn($product);

        $result = $this->service->delete($data);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_delete_throws_exception_when_product_not_found()
    {
        $data = [
            'id' => 1,
            'business_id' => 123,
        ];

        $this->repoMock->shouldReceive('findById')->with($data)->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('product::messages.not_found_for_update'));

        $this->service->delete($data);
    }
}
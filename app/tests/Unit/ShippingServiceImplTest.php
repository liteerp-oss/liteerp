<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use Core\Shipping\Domain\Entities\Shipping;
use Core\Shipping\Domain\Repositories\ShippingRepositoryInterface;
use Core\Shipping\Infrastructure\Services\ShippingServiceImpl;
use Tests\TestCase;
use Mockery;

class ShippingServiceImplTest extends TestCase
{
    protected $repoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoMock = Mockery::mock(ShippingRepositoryInterface::class);
        $this->service = new ShippingServiceImpl($this->repoMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_returns_shipping_when_name_not_used()
    {
        $data = [
            'name' => 'Test Shipping',
            'code' => 'TS001',
            'business_id' => 1
        ];

        $this->repoMock->shouldReceive('findByName')
            ->with($data)
            ->andReturn(null);

        $shipping = Shipping::fromArray($data);
        $shipping->id = 1;

        $this->repoMock->shouldReceive('create')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof Shipping &&
                       $arg->name === 'Test Shipping' &&
                       $arg->code === 'TS001';
            }))
            ->andReturn($shipping);

        $result = $this->service->create($data);

        $this->assertInstanceOf(Shipping::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Test Shipping', $result->name);
    }

    public function test_create_throws_exception_when_name_already_used()
    {
        $data = [
            'name' => 'Test Shipping',
            'code' => 'TS001',
            'business_id' => 1
        ];

        $existingShipping = Shipping::fromArray(['id' => 1, 'name' => 'Test Shipping', 'code' => 'TS001', 'business_id' => 1]);

        $this->repoMock->shouldReceive('findByName')
            ->with($data)
            ->andReturn($existingShipping);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('shippings::messages.name_used'));

        $this->service->create($data);
    }

    public function test_show_returns_shipping()
    {
        $data = ['id' => 1];
        $shipping = Shipping::fromArray(['id' => 1, 'name' => 'Test Shipping', 'code' => 'TS001', 'business_id' => 1]);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($shipping);

        $result = $this->service->show($data);

        $this->assertInstanceOf(Shipping::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_show_throws_exception_when_shipping_not_found()
    {
        $data = ['id' => 1];

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('shippings::messages.not_found'));

        $this->service->show($data);
    }

    public function test_update_returns_updated_shipping()
    {
        $data = [
            'id' => 1,
            'name' => 'Updated Shipping',
            'code' => 'US001',
            'logo' => 'new-logo.png',
            'active' => false,
            'business_id' => 1
        ];

        $existingShipping = Shipping::fromArray(['id' => 1, 'name' => 'Old Shipping', 'code' => 'OS001', 'business_id' => 1]);

        $this->repoMock->shouldReceive('findByName')
            ->with($data)
            ->andReturn(null);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($existingShipping);

        $updatedShipping = Shipping::fromArray($data);
        $updatedShipping->id = 1;

        $this->repoMock->shouldReceive('update')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof Shipping &&
                       $arg->name === 'Updated Shipping' &&
                       $arg->code === 'US001' &&
                       $arg->logo === 'new-logo.png' &&
                       $arg->active === false;
            }))
            ->andReturn($updatedShipping);

        $result = $this->service->update($data);

        $this->assertInstanceOf(Shipping::class, $result);
        $this->assertEquals('Updated Shipping', $result->name);
        $this->assertEquals('US001', $result->code);
    }

    public function test_update_throws_exception_when_name_already_used_by_another_shipping()
    {
        $data = [
            'id' => 1,
            'name' => 'Updated Shipping',
            'code' => 'US001',
            'business_id' => 1
        ];

        $anotherShipping = Shipping::fromArray(['id' => 2, 'name' => 'Updated Shipping', 'code' => 'US001', 'business_id' => 1]);

        $this->repoMock->shouldReceive('findByName')
            ->with($data)
            ->andReturn($anotherShipping);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('shippings::messages.name_used'));

        $this->service->update($data);
    }

    public function test_update_throws_exception_when_shipping_not_found()
    {
        $data = [
            'id' => 1,
            'name' => 'Updated Shipping',
            'code' => 'US001',
            'business_id' => 1
        ];

        $this->repoMock->shouldReceive('findByName')
            ->with($data)
            ->andReturn(null);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('shippings::messages.not_found'));

        $this->service->update($data);
    }

    public function test_delete_returns_deleted_shipping()
    {
        $data = ['id' => 1];
        $shipping = Shipping::fromArray(['id' => 1, 'name' => 'Test Shipping', 'code' => 'TS001', 'business_id' => 1]);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($shipping);

        $this->repoMock->shouldReceive('delete')
            ->with($shipping)
            ->andReturn($shipping);

        $result = $this->service->delete($data);

        $this->assertInstanceOf(Shipping::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_delete_throws_exception_when_shipping_not_found()
    {
        $data = ['id' => 1];

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('shippings::messages.not_found'));

        $this->service->delete($data);
    }
}
<?php

namespace Tests\Unit;

use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\Shipping\Application\UseCases\CreateShipping;
use Core\Shipping\Domain\Entities\Shipping;
use Core\Shipping\Domain\Services\ShippingService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateShippingTest extends TestCase
{
    protected $serviceMock;
    protected $hookDispatcherMock;
    protected $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        DB::shouldReceive('beginTransaction')->andReturn(null);
        DB::shouldReceive('commit')->andReturn(null);

        $this->serviceMock = Mockery::mock(ShippingService::class);
        $this->hookDispatcherMock = Mockery::mock(HookDispatcher::class);
        $this->useCase = new CreateShipping($this->serviceMock, $this->hookDispatcherMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_shipping_successfully()
    {
        $data = [
            'name' => 'Test Shipping',
            'code' => 'TS001',
            'logo' => 'logo.png',
            'active' => true,
            'business_id' => 1,
            'user_id' => 1
        ];

        $shipping = Shipping::fromArray([
            'name' => 'Test Shipping',
            'code' => 'TS001',
            'logo' => 'logo.png',
            'active' => true,
            'business_id' => 1
        ]);
        $shipping->id = 1;

        // Mock hook dispatcher for BEFORE
        $this->hookDispatcherMock->shouldReceive('dispatch')
            ->with(Mockery::on(function ($context) {
                return $context instanceof HookContext &&
                       $context->timing === 'before' &&
                       $context->module === 'Shipping';
            }))
            ->andReturn($data);

        // Mock hook dispatcher for AFTER
        $this->hookDispatcherMock->shouldReceive('dispatch')
            ->with(Mockery::on(function ($context) {
                return $context instanceof HookContext &&
                       $context->timing === 'after' &&
                       $context->module === 'Shipping';
            }))
            ->andReturn($data);

        $this->serviceMock->shouldReceive('create')
            ->with(Mockery::on(function ($arg) {
                return is_array($arg) &&
                       $arg['name'] === 'Test Shipping' &&
                       $arg['code'] === 'TS001' &&
                       $arg['business_id'] === 1;
            }))
            ->andReturn($shipping);

        Event::shouldReceive('dispatch')
            ->with('erp.shipping.create', Mockery::on(function ($arg) {
                return is_array($arg) &&
                       isset($arg['user_id']) &&
                       isset($arg['business_id']);
            }))
            ->once();

        $result = $this->useCase->handle($data);

        $this->assertIsArray($result);
        $this->assertEquals('Test Shipping', $result['name']);
        $this->assertEquals('TS001', $result['code']);
    }
}

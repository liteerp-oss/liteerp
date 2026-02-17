<?php

namespace Tests\Unit;

use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\Supplier\Application\UseCases\CreateSupplier;
use Core\Supplier\Domain\Entities\Supplier;
use Core\Supplier\Domain\Services\SupplierService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateSupplierTest extends TestCase
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

        $this->serviceMock = Mockery::mock(SupplierService::class);
        $this->hooksMock = Mockery::mock(HookDispatcher::class);
        $this->useCase = new CreateSupplier($this->serviceMock, $this->hooksMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_supplier_successfully()
    {
        $data = [
            'business_id' => 123,
            'unit_name' => 'Test Supplier',
            'email' => 'supplier@example.com',
            'phone' => '1234567890',
            'created_by' => 1,
        ];

        $supplier = Supplier::fromArray($data);
        $supplier->id = 1;

        // Mock hooks dispatch - return input data for first call, merged data for second
        $this->hooksMock->shouldReceive('dispatch')
            ->andReturn($data, array_merge($supplier->toArray(), $data));

        $this->serviceMock->shouldReceive('create')->andReturn($supplier);

        Event::shouldReceive('dispatch')->once();

        $result = $this->useCase->handle($data);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Test Supplier', $result['unit_name']);
    }
}

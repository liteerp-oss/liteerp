<?php

namespace Tests\Unit;

use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\Warehouse\Application\UseCases\CreateWarehouse;
use Core\Warehouse\Domain\Entities\Warehouse;
use Core\Warehouse\Domain\Services\WarehouseService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateWarehouseTest extends TestCase
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

        $this->serviceMock = Mockery::mock(WarehouseService::class);
        $this->hooksMock = Mockery::mock(HookDispatcher::class);
        $this->useCase = new CreateWarehouse($this->serviceMock, $this->hooksMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_warehouse_successfully()
    {
        $data = [
            'name' => 'Main Warehouse',
            'address' => '123 Main St',
            'business_id' => 123,
            'role' => null,
            'user_id' => 1,
            'id' => null,
            'active' => true,
        ];

        $warehouse = Warehouse::fromArray([
            'name' => 'Main Warehouse',
            'address' => '123 Main St',
            'business_id' => 123,
            'created_by' => 1,
        ]);
        $warehouse->id = 1;

        $afterData = [...$data, ...$warehouse->toArray()];
        $this->hooksMock->shouldReceive('dispatch')
            ->twice()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data, $afterData);
        $this->serviceMock->shouldReceive('create')->andReturn($warehouse);

        Event::shouldReceive('dispatch')->once();

        $result = $this->useCase->handle($data);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Main Warehouse', $result['name']);
    }
}

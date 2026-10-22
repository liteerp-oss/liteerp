<?php

namespace Tests\Unit;

use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\Product\Application\UseCases\CreateProduct;
use Core\Product\Domain\Entities\Product;
use Core\Product\Domain\Services\ProductService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateProductTest extends TestCase
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

        $this->serviceMock = Mockery::mock(ProductService::class);
        $this->hooksMock = Mockery::mock(HookDispatcher::class);
        $this->useCase = new CreateProduct($this->serviceMock, $this->hooksMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_product_successfully()
    {
        $data = [
            'business_id' => 123,
            'category_id' => 456,
            'sku' => 'PROD-001',
            'name' => 'Test Product',
            'unit' => 'pcs',
            'user_id' => 1,
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

        // Mock hooks dispatch - return input data for first call, merged data for second
        $this->hooksMock->shouldReceive('dispatch')
            ->andReturn($data, array_merge($data, $product->toArray()));

        $this->serviceMock->shouldReceive('create')->andReturn($product);

        Event::shouldReceive('dispatch')
            ->with('erp.product.create', Mockery::type('array'))
            ->once();

        Event::shouldReceive('dispatch')
            ->with('erp.activitylog.create', Mockery::on(function ($payload) use ($product, $data) {
                return $payload === [
                    'user_id' => $data['user_id'],
                    'business_id' => $product->business_id,
                    'type' => 'product',
                    'id' => $product->id,
                    'data' => $product->toArray(),
                ];
            }))
            ->once();

        $result = $this->useCase->handle($data);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Test Product', $result['name']);
    }
}
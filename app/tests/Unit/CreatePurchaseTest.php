<?php

namespace Tests\Unit;

use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\Purchase\Application\UseCases\CreatePurchase;
use Core\Purchase\Domain\Entities\Purchase;
use Core\Purchase\Domain\Services\PurchaseService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreatePurchaseTest extends TestCase
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

        $this->serviceMock = Mockery::mock(PurchaseService::class);
        $this->hookDispatcherMock = Mockery::mock(HookDispatcher::class);
        $this->useCase = new CreatePurchase($this->serviceMock, $this->hookDispatcherMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_purchase_successfully()
    {
        $data = [
            'business_id' => 1,
            'supplier_id' => 2,
            'user_id' => 3,
            'purchase_date' => '2024-01-15',
            'expected_date' => '2024-01-20',
            'note' => 'Test purchase',
            'shipping_fee' => 25.00,
            'payment_method' => 'cash',
            'username' => 'tester'
        ];

        $purchase = new Purchase(
            business_id: 1,
            supplier_id: 2,
            created_by: 3,
            purchase_date: '2024-01-15',
            expected_date: '2024-01-20',
            note: 'Test purchase',
            shipping_fee: 25.00,
            payment_method: 'cash'
        );
        $purchase->id = 1;
        $purchase->approved_by = null;

        // Mock hook dispatcher for BEFORE
        $this->hookDispatcherMock->shouldReceive('dispatch')
            ->with(Mockery::on(function ($context) {
                return $context instanceof HookContext &&
                       $context->timing === 'before' &&
                       $context->module === 'Purchase';
            }))
            ->andReturn($data);

        // Mock hook dispatcher for AFTER
        $this->hookDispatcherMock->shouldReceive('dispatch')
            ->with(Mockery::on(function ($context) {
                return $context instanceof HookContext &&
                       $context->timing === 'after' &&
                       $context->module === 'Purchase';
            }))
            ->andReturn($data);

        $this->serviceMock->shouldReceive('create')
            ->with(Mockery::on(function ($arg) {
                return is_array($arg) &&
                       $arg['business_id'] === 1 &&
                       $arg['supplier_id'] === 2 &&
                       $arg['created_by'] === 3;
            }))
            ->andReturn($purchase);

        Event::shouldReceive('dispatch')
            ->with('erp.purchase.create', Mockery::on(function ($arg) {
                return is_array($arg) &&
                       isset($arg['user_id']) &&
                       isset($arg['business_id']);
            }))
            ->once();

        Event::shouldReceive('dispatch')
            ->with('erp.notification.many', Mockery::on(function ($arg) {
                return is_array($arg) &&
                       $arg['type'] === 'created' &&
                       $arg['entity_type'] === 'purchase';
            }))
            ->once();

        Event::shouldReceive('dispatch')
            ->with('erp.notification.create', Mockery::on(function ($arg) {
                return is_array($arg) &&
                       $arg['type'] === 'created' &&
                       $arg['entity_type'] === 'purchase';
            }))
            ->once();

        $result = $this->useCase->handle($data);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['business_id']);
        $this->assertEquals(2, $result['supplier_id']);
    }
}

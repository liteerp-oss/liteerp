<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use Core\Purchase\Domain\Entities\Purchase;
use Core\Purchase\Domain\Repositories\PurchaseRepositoryInterface;
use Core\Purchase\Infrastructure\Services\PurchaseServiceImpl;
use Tests\TestCase;
use Mockery;

class PurchaseServiceImplTest extends TestCase
{
    protected $repoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoMock = Mockery::mock(PurchaseRepositoryInterface::class);
        $this->service = new PurchaseServiceImpl($this->repoMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_returns_purchase()
    {
        $data = [
            'business_id' => 1,
            'supplier_id' => 2,
            'created_by' => 3,
            'purchase_date' => '2024-01-15',
            'expected_date' => '2024-01-20',
            'note' => 'Test purchase',
            'shipping_fee' => 25.00,
            'payment_method' => 'cash'
        ];

        $purchase = Purchase::fromArray($data);
        $purchase->id = 1;
        $purchase->approved_by = null;

        $this->repoMock->shouldReceive('create')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof Purchase &&
                       $arg->business_id === 1 &&
                       $arg->supplier_id === 2 &&
                       $arg->approved_by === null;
            }))
            ->andReturn($purchase);

        $result = $this->service->create($data);

        $this->assertInstanceOf(Purchase::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals(1, $result->business_id);
        $this->assertNull($result->approved_by);
    }

    public function test_show_returns_purchase_data()
    {
        $data = ['id' => 1];
        $expectedData = [
            'id' => 1,
            'business_id' => 1,
            'supplier_id' => 2,
            'status' => 'draft',
            'items' => []
        ];

        $this->repoMock->shouldReceive('findByIdWithFullData')
            ->with($data)
            ->andReturn($expectedData);

        $result = $this->service->show($data);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('draft', $result['status']);
    }

    public function test_show_throws_exception_when_purchase_not_found()
    {
        $data = ['id' => 1];

        $this->repoMock->shouldReceive('findByIdWithFullData')
            ->with($data)
            ->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('purchase::messages.not_found'));

        $this->service->show($data);
    }

    public function test_find_one_by_id_returns_purchase()
    {
        $data = ['id' => 1];
        $purchase = Purchase::fromArray([
            'id' => 1, 
            'business_id' => 1,
            'purchase_date' => '',
            'expected_date' => ''
        ]);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($purchase);

        $result = $this->service->findOneById($data);

        $this->assertInstanceOf(Purchase::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_find_one_by_id_throws_exception_when_purchase_not_found()
    {
        $data = ['id' => 1];

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('purchase::messages.not_found'));

        $this->service->findOneById($data);
    }

    public function test_update_draft_status_updates_purchase_information()
    {
        $data = [
            'id' => 1,
            'status' => 'draft',
            'supplier_id' => 5,
            'purchase_date' => '2024-02-01',
            'expected_date' => '2024-02-05',
            'note' => 'Updated note',
            'shipping_fee' => 30.00,
            'payment_method' => 'credit_card'
        ];

        $existingPurchase = Purchase::fromArray([
            'id' => 1,
            'business_id' => 1,
            'status' => 'draft',
            'purchase_date' => '',
            'expected_date' => ''
        ]);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($existingPurchase);

        $this->repoMock->shouldReceive('update')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof Purchase &&
                       $arg->supplier_id === 5 &&
                       $arg->status === 'draft';
            }))
            ->andReturn($existingPurchase);

        $result = $this->service->update($data);

        $this->assertInstanceOf(Purchase::class, $result);
        $this->assertEquals('draft', $result->status);
        $this->assertEquals(5, $result->supplier_id);
    }

    public function test_update_requested_status_changes_status_to_requested()
    {
        $data = [
            'id' => 1,
            'status' => 'requested'
        ];

        $existingPurchase = Purchase::fromArray([
            'id' => 1,
            'business_id' => 1,
            'status' => 'draft',
            'purchase_date' => '',
            'expected_date' => ''
        ]);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($existingPurchase);

        $this->repoMock->shouldReceive('update')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof Purchase &&
                       $arg->status === 'requested';
            }))
            ->andReturn($existingPurchase);

        $result = $this->service->update($data);

        $this->assertInstanceOf(Purchase::class, $result);
        $this->assertEquals('requested', $result->status);
    }

    public function test_update_approved_status_changes_status_to_approved()
    {
        $data = [
            'id' => 1,
            'status' => 'approved',
            'approved_by' => 10
        ];

        $existingPurchase = Purchase::fromArray([
            'id' => 1,
            'business_id' => 1,
            'status' => 'requested',
            'purchase_date' => '',
            'expected_date' => ''
        ]);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($existingPurchase);

        $this->repoMock->shouldReceive('update')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof Purchase &&
                       $arg->status === 'approved' &&
                       $arg->approved_by === 10;
            }))
            ->andReturn($existingPurchase);

        $result = $this->service->update($data);

        $this->assertInstanceOf(Purchase::class, $result);
        $this->assertEquals('approved', $result->status);
        $this->assertEquals(10, $result->approved_by);
    }

    public function test_update_cancelled_status_changes_status_to_cancelled()
    {
        $data = [
            'id' => 1,
            'status' => 'cancelled'
        ];

        $existingPurchase = Purchase::fromArray([
            'id' => 1,
            'business_id' => 1,
            'status' => 'approved',
            'purchase_date' => '',
            'expected_date' => ''
        ]);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($existingPurchase);

        $this->repoMock->shouldReceive('update')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof Purchase &&
                       $arg->status === 'cancelled';
            }))
            ->andReturn($existingPurchase);

        $result = $this->service->update($data);

        $this->assertInstanceOf(Purchase::class, $result);
        $this->assertEquals('cancelled', $result->status);
    }

    public function test_update_throws_exception_when_purchase_not_found()
    {
        $data = [
            'id' => 1,
            'status' => 'draft'
        ];

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('purchase::messages.not_found'));

        $this->service->update($data);
    }

    public function test_update_throws_exception_for_invalid_status_transition_from_requested_to_draft()
    {
        $data = [
            'id' => 1,
            'status' => 'draft'
        ];

        $existingPurchase = Purchase::fromArray([
            'id' => 1,
            'business_id' => 1,
            'status' => 'requested',
            'purchase_date' => '',
            'expected_date' => ''
        ]);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($existingPurchase);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('purchase::messages.status_transition_invalid'));

        $this->service->update($data);
    }

    public function test_update_throws_exception_for_invalid_status_transition_from_draft_to_approved()
    {
        $data = [
            'id' => 1,
            'status' => 'approved'
        ];

        $existingPurchase = Purchase::fromArray([
            'id' => 1,
            'business_id' => 1,
            'status' => 'draft',
            'purchase_date' => '',
            'expected_date' => ''
        ]);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($existingPurchase);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('purchase::messages.status_transition_invalid'));

        $this->service->update($data);
    }

    public function test_update_throws_exception_for_invalid_status()
    {
        $data = [
            'id' => 1,
            'status' => 'invalid_status'
        ];

        $existingPurchase = Purchase::fromArray([
            'id' => 1,
            'business_id' => 1,
            'status' => 'draft',
            'purchase_date' => '',
            'expected_date' => ''
        ]);

        $this->repoMock->shouldReceive('findById')
            ->with($data)
            ->andReturn($existingPurchase);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('purchase::messages.status_invalid'));

        $this->service->update($data);
    }
}
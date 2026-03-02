<?php

namespace Tests\Unit;

use Core\InvoiceIn\Application\UseCases\CreateInvoiceIn;
use Core\InvoiceIn\Domain\Services\InvoiceInService;
use Core\InvoiceIn\Domain\Entities\InvoiceIn;
use App\Supports\Hooks\HookDispatcher;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookTiming;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class CreateInvoiceInTest extends TestCase
{
    private CreateInvoiceIn $useCase;
    private $service;
    private $hookDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
        DB::shouldReceive('beginTransaction')->andReturn(null);
        DB::shouldReceive('commit')->andReturn(null);

        $this->service = Mockery::mock(InvoiceInService::class);
        $this->hookDispatcher = Mockery::mock(HookDispatcher::class);

        $this->useCase = new CreateInvoiceIn(
            $this->service,
            $this->hookDispatcher
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_invoice_in_successfully()
    {
        $data = [
            'business_id' => 1,
            'purchase_id' => 2,
            'subtotal' => 100.00,
            'tax' => 10.00,
            'discount' => 5.00,
            'total' => 105.00,
            'invoice_date' => '2024-01-15',
            'due_date' => '2024-02-15',
            'image' => 'invoice.jpg',
            'created_by' => 3,
            'username' => 'tester'
        ];

        $createdInvoiceIn = new InvoiceIn(
            id: 1,
            business_id: 1,
            document_no: 'INV-1-20240115-001',
            purchase_id: 2,
            approved_by: null,
            subtotal: 100.00,
            tax: 10.00,
            discount: 5.00,
            total: 105.00,
            invoice_date: '2024-01-15',
            due_date: '2024-02-15',
            approved: false,
            payment_status: 'pending',
            image: 'invoice.jpg',
            amount_paid: 0
        );

        $this->hookDispatcher
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function ($context) {
                return $context instanceof HookContext &&
                       $context->action === HookAction::CREATE &&
                       $context->phase === HookPhase::RESPONSE &&
                       $context->timing === HookTiming::BEFORE;
            }))
            ->andReturn($data);

        $this->service
            ->shouldReceive('create')
            ->with(Mockery::on(function ($createData) {
                return $createData['business_id'] === 1 &&
                       $createData['purchase_id'] === 2 &&
                       $createData['total'] === 105.00;
            }))
            ->andReturn($createdInvoiceIn);

        $this->hookDispatcher
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function ($context) {
                return $context instanceof HookContext &&
                       $context->action === HookAction::CREATE &&
                       $context->phase === HookPhase::RESPONSE &&
                       $context->timing === HookTiming::AFTER;
            }))
            ->andReturn($data);

        Event::shouldReceive('dispatch')->twice();

        $result = $this->useCase->handle($data);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['business_id']);
        $this->assertEquals(2, $result['purchase_id']);
    }

    public function test_handle_handles_service_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Service error');

        $data = [
            'business_id' => 1,
            'purchase_id' => 2,
            'subtotal' => 100.00,
            'total' => 105.00
        ];

        $this->hookDispatcher
            ->shouldReceive('dispatch')
            ->andReturn($data);

        $this->service
            ->shouldReceive('create')
            ->andThrow(new \Exception('Service error'));

        $this->useCase->handle($data);
    }
}

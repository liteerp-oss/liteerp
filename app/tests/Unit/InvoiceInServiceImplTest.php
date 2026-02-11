<?php

namespace Tests\Unit;

use Core\InvoiceIn\Infrastructure\Services\InvoiceInServiceImpl;
use Core\InvoiceIn\Domain\Entities\InvoiceIn;
use Core\InvoiceIn\Domain\Repositories\InvoiceInRepositoryInterface;
use App\Exceptions\BadException;
use Mockery;
use Tests\TestCase;

class InvoiceInServiceImplTest extends TestCase
{
    private InvoiceInServiceImpl $service;
    private $invoiceInRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->invoiceInRepository = Mockery::mock(InvoiceInRepositoryInterface::class);

        $this->service = new InvoiceInServiceImpl($this->invoiceInRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_invoice_in_success()
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
            'image' => 'invoice.jpg'
        ];

        $expectedInvoiceIn = new InvoiceIn(
            id: 1,
            business_id: 1,
            document_no: 'INV-1-' . date('Ymd') . '-001',
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

        $this->invoiceInRepository
            ->shouldReceive('create')
            ->with(Mockery::on(function ($invoiceIn) use ($expectedInvoiceIn) {
                return $invoiceIn->business_id === $expectedInvoiceIn->business_id &&
                       $invoiceIn->purchase_id === $expectedInvoiceIn->purchase_id &&
                       $invoiceIn->subtotal === $expectedInvoiceIn->subtotal &&
                       $invoiceIn->total === $expectedInvoiceIn->total;
            }))
            ->andReturn($expectedInvoiceIn);

        $result = $this->service->create($data);

        $this->assertEquals($expectedInvoiceIn, $result);
    }

    public function test_index_returns_invoice_ins_array()
    {
        $filters = ['business_id' => 1];
        $invoiceIns = [
            new InvoiceIn(
                id: 1,
                business_id: 1,
                document_no: null,
                purchase_id: 2,
                approved_by: null,
                subtotal: 0,
                tax: 0,
                discount: 0,
                total: 0,
                invoice_date: null,
                due_date: null,
                approved: false,
                payment_status: 'pending',
                image: null,
                amount_paid: 0
            ),
            new InvoiceIn(
                id: 2,
                business_id: 1,
                document_no: null,
                purchase_id: 3,
                approved_by: null,
                subtotal: 0,
                tax: 0,
                discount: 0,
                total: 0,
                invoice_date: null,
                due_date: null,
                approved: false,
                payment_status: 'pending',
                image: null,
                amount_paid: 0
            )
        ];

        $this->invoiceInRepository
            ->shouldReceive('all')
            ->with($filters)
            ->andReturn($invoiceIns);

        $result = $this->service->index($filters);

        $this->assertEquals($invoiceIns, $result);
    }

    public function test_update_invoice_in_success()
    {
        $data = [
            'id' => 1,
            'invoice_date' => '2024-01-20',
            'due_date' => '2024-02-20',
            'approved' => true,
            'approved_by' => 5,
            'payment_status' => 'paid',
            'amount_paid' => 105.00
        ];

        $existingInvoiceIn = new InvoiceIn(
            id: 1,
            business_id: 1,
            document_no: 'INV-1001',
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
            image: null,
            amount_paid: 0.00
        );

        $updatedInvoiceIn = new InvoiceIn(
            id: 1,
            business_id: 1,
            document_no: 'INV-1001',
            purchase_id: 2,
            approved_by: 5,
            subtotal: 100.00,
            tax: 10.00,
            discount: 5.00,
            total: 105.00,
            invoice_date: '2024-01-20',
            due_date: '2024-02-20',
            approved: true,
            payment_status: 'paid',
            image: null,
            amount_paid: 105.00
        );

        $this->invoiceInRepository
            ->shouldReceive('findById')
            ->with($data)
            ->andReturn($existingInvoiceIn);

        $this->invoiceInRepository
            ->shouldReceive('update')
            ->with(Mockery::on(function ($invoiceIn) {
                return $invoiceIn->invoice_date === '2024-01-20' &&
                       $invoiceIn->approved === true &&
                       $invoiceIn->payment_status === 'paid' &&
                       $invoiceIn->amount_paid === 105.00;
            }))
            ->andReturn($updatedInvoiceIn);

        $result = $this->service->update($data);

        $this->assertEquals($updatedInvoiceIn, $result);
    }

    public function test_update_invoice_in_fails_when_not_found()
    {
        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('invoicein::messages.not_found'));

        $data = ['id' => 999];

        $this->invoiceInRepository
            ->shouldReceive('findById')
            ->with($data)
            ->andReturn(null);

        $this->service->update($data);
    }

    public function test_update_invoice_in_fails_when_trying_to_unapprove_approved_invoice()
    {
        $this->expectException(BadException::class);

        $data = [
            'id' => 1,
            'approved' => false
        ];

        $existingInvoiceIn = new InvoiceIn(
            id: 1,
            business_id: 1,
            document_no: 'INV-1001',
            purchase_id: 2,
            approved_by: null,
            subtotal: 0,
            tax: 0,
            discount: 0,
            total: 0,
            invoice_date: null,
            due_date: null,
            approved: true,
            payment_status: 'pending',
            image: null,
            amount_paid: 0
        );

        $this->invoiceInRepository
            ->shouldReceive('findById')
            ->with($data)
            ->andReturn($existingInvoiceIn);

        $this->service->update($data);
    }

    public function test_show_returns_invoice_data()
    {
        $data = ['id' => 1];
        $invoiceData = ['id' => 1, 'document_no' => 'INV-1001'];

        $this->invoiceInRepository
            ->shouldReceive('findByIdWithFullData')
            ->with($data)
            ->andReturn($invoiceData);

        $result = $this->service->show($data);

        $this->assertEquals($invoiceData, $result);
    }

    public function test_show_fails_when_not_found()
    {
        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('invoicein::messages.not_found'));

        $data = ['id' => 999];

        $this->invoiceInRepository
            ->shouldReceive('findByIdWithFullData')
            ->with($data)
            ->andReturn(null);

        $this->service->show($data);
    }

    public function test_get_by_id_success()
    {
        $data = ['id' => 1];
        $invoiceIn = new InvoiceIn(
            id: 1,
            business_id: 1,
            document_no: 'INV-1001',
            purchase_id: 2,
            approved_by: null,
            subtotal: 0,
            tax: 0,
            discount: 0,
            total: 0,
            invoice_date: null,
            due_date: null,
            approved: false,
            payment_status: 'pending',
            image: null,
            amount_paid: 0
        );

        $this->invoiceInRepository
            ->shouldReceive('findById')
            ->with($data)
            ->andReturn($invoiceIn);

        $result = $this->service->getById($data);

        $this->assertEquals($invoiceIn, $result);
    }

    public function test_get_by_purchase_id_success()
    {
        $data = ['purchase_id' => 2];
        $invoiceIn = new InvoiceIn(
            id: 1,
            business_id: 1,
            document_no: 'INV-1001',
            purchase_id: 2,
            approved_by: null,
            subtotal: 0,
            tax: 0,
            discount: 0,
            total: 0,
            invoice_date: null,
            due_date: null,
            approved: false,
            payment_status: 'pending',
            image: null,
            amount_paid: 0
        );

        $this->invoiceInRepository
            ->shouldReceive('findByPurchaseId')
            ->with($data)
            ->andReturn($invoiceIn);

        $result = $this->service->getByPurchaseId($data);

        $this->assertEquals($invoiceIn, $result);
    }

    public function test_find_by_id_success()
    {
        $data = ['id' => 1];
        $invoiceIn = new InvoiceIn(
            id: 1,
            business_id: 1,
            document_no: 'INV-1001',
            purchase_id: 2,
            approved_by: null,
            subtotal: 0,
            tax: 0,
            discount: 0,
            total: 0,
            invoice_date: null,
            due_date: null,
            approved: false,
            payment_status: 'pending',
            image: null,
            amount_paid: 0
        );

        $this->invoiceInRepository
            ->shouldReceive('findById')
            ->with($data)
            ->andReturn($invoiceIn);

        $result = $this->service->findById($data);

        $this->assertEquals($invoiceIn, $result);
    }

    public function test_find_by_id_fails_when_not_found()
    {
        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('invoicein::messages.not_found'));

        $data = ['id' => 999];

        $this->invoiceInRepository
            ->shouldReceive('findById')
            ->with($data)
            ->andReturn(null);

        $this->service->findById($data);
    }

    public function test_change_to_un_approved_success()
    {
        $data = ['purchase_id' => 2];

        $existingInvoiceIn = new InvoiceIn(
            id: 1,
            business_id: 1,
            document_no: 'INV-1001',
            purchase_id: 2,
            approved_by: null,
            subtotal: 0,
            tax: 0,
            discount: 0,
            total: 0,
            invoice_date: null,
            due_date: null,
            approved: true,
            payment_status: 'pending',
            image: null,
            amount_paid: 0
        );

        $updatedInvoiceIn = new InvoiceIn(
            id: 1,
            business_id: 1,
            document_no: 'INV-1001',
            purchase_id: 2,
            approved_by: null,
            subtotal: 0,
            tax: 0,
            discount: 0,
            total: 0,
            invoice_date: null,
            due_date: null,
            approved: false,
            payment_status: 'pending',
            image: null,
            amount_paid: 0
        );

        $this->invoiceInRepository
            ->shouldReceive('findByPurchaseId')
            ->with($data)
            ->andReturn($existingInvoiceIn);

        $this->invoiceInRepository
            ->shouldReceive('update')
            ->with(Mockery::on(function ($invoiceIn) {
                return $invoiceIn->approved === false;
            }))
            ->andReturn($updatedInvoiceIn);

        $result = $this->service->changeToUnApproved($data);

        $this->assertEquals($updatedInvoiceIn, $result);
    }

    public function test_change_to_un_approved_fails_when_not_found()
    {
        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('invoicein::messages.not_found'));

        $data = ['purchase_id' => 999];

        $this->invoiceInRepository
            ->shouldReceive('findByPurchaseId')
            ->with($data)
            ->andReturn(null);

        $this->service->changeToUnApproved($data);
    }
}
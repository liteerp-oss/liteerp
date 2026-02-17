<?php

namespace Tests\Unit;

use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\CustomerGroup\Application\DTOs\CreateCustomerGroupRequest;
use Core\CustomerGroup\Application\UseCases\CreateCustomerGroup;
use Core\CustomerGroup\Domain\Services\CustomerGroupService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class CreateCustomerGroupTest extends TestCase
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

        $this->serviceMock = Mockery::mock(CustomerGroupService::class);
        $this->hookDispatcherMock = Mockery::mock(HookDispatcher::class);
        $this->useCase = new CreateCustomerGroup($this->serviceMock, $this->hookDispatcherMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_customer_group_successfully()
    {
        $dto = new CreateCustomerGroupRequest(business_id: 1, name: 'VIP Customers', created_by: 1, id: null);

        $customerGroup = \Core\CustomerGroup\Domain\Entities\CustomerGroup::fromArray([
            'id' => 1,
            'business_id' => 1,
            'name' => 'VIP Customers',
        ]);

        $afterData = [
            'id' => 1,
            'business_id' => 1,
            'name' => 'VIP Customers',
            'created_by' => 1,
            'user_id' => 1,
        ];

        $this->hookDispatcherMock->shouldReceive('dispatch')
            ->twice()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($dto->toArray(), $afterData);

        $this->serviceMock->shouldReceive('create')
            ->with(Mockery::on(function ($arg) {
                return is_array($arg) &&
                       $arg['business_id'] === 1 &&
                       $arg['name'] === 'VIP Customers';
            }))
            ->andReturn($customerGroup);

        Event::shouldReceive('dispatch')
            ->with('erp.customergroup.create', Mockery::on(function ($arg) {
                return is_array($arg) &&
                       isset($arg['business_id']) &&
                       $arg['name'] === 'VIP Customers';
            }))
            ->once();

        $result = $this->useCase->handle([
            ...$dto->toArray(),
            'user_id' => 1,
        ]);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
        $this->assertSame('VIP Customers', $result['name']);
        $this->assertSame(1, $result['business_id']);
    }
}

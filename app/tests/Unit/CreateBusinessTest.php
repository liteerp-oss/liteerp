<?php

namespace Tests\Unit;

use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\Business\Application\DTOs\CreateBusinessRequest;
use Core\Business\Application\UseCases\CreateBusiness;
use Core\Business\Domain\Entities\Business;
use Core\Business\Domain\Services\BusinessService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;

class CreateBusinessTest extends TestCase
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

        // Mock the Auth facade completely
        $user = (object) ['id' => 1];
        $guard = Mockery::mock();
        $guard->shouldReceive('user')->andReturn($user);
        
        $auth = Mockery::mock();
        $auth->shouldReceive('guard')->with('sanctum')->andReturn($guard);
        $auth->shouldReceive('guard')->andReturn($guard);
        $auth->shouldReceive('hasResolvedGuards')->andReturn(true);
        
        $this->app->instance('auth', $auth);

        $this->serviceMock = Mockery::mock(BusinessService::class);
        $this->hooksMock = Mockery::mock(HookDispatcher::class);
        $this->useCase = new CreateBusiness($this->serviceMock, $this->hooksMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_business_successfully()
    {
        $dto = new CreateBusinessRequest(
            name: 'Test Business',
            address: '123 Main St',
            user_id: 1,
            tax_code: '123456789',
            phone: '1234567890',
            email: 'business@example.com',
            logo_url: null,
            bank_name: 'Test Bank',
            bank_account_number: '1234567890',
            bank_account_name: 'Test Account'
        );

        $business = Business::fromArray([
            'name' => 'Test Business',
            'address' => '123 Main St',
            'tax_code' => '123456789',
            'phone' => '1234567890',
            'email' => 'business@example.com',
            'bank_name' => 'Test Bank',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Test Account',
        ]);
        $business->id = 1;

        // Mock the authenticated user is already done in setUp

        $this->hooksMock->shouldReceive('dispatch')
            ->twice()
            ->with(Mockery::type(HookContext::class))
            ->andReturn(
                $dto->toArray(),
                [...$dto->toArray(), ...$business->toArray()]
            );
        $this->serviceMock->shouldReceive('create')->andReturn($business);

        Event::shouldReceive('dispatch')->once();

        $result = $this->useCase->handle($dto->toArray());

        $this->assertInstanceOf(Business::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Test Business', $result->name);
    }
}

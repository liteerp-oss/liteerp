<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\User\Application\UseCases\CreateUser;
use Core\User\Domain\Entities\User;
use Core\User\Domain\Services\UserService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;

class CreateUserTest extends TestCase
{
    protected $serviceMock;
    protected $hooksMock;
    protected $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->serviceMock = Mockery::mock(UserService::class);
        $this->hooksMock = Mockery::mock(HookDispatcher::class);
        $this->useCase = new CreateUser($this->serviceMock, $this->hooksMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_throws_exception_when_user_exists_in_business()
    {
        $data = [
            'email' => 'test@example.com',
            'user_id' => 1,
            'business_id' => 1,
            'role' => 'member',
            'id' => 1
        ];
        $existingUser = new User(1, 'test@example.com', 'admin', 123);

        $this->hooksMock->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data);

        $this->serviceMock->shouldReceive('getByEmail')->andReturn($existingUser);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.is_exists_on_business'));

        $this->useCase->handle($data);
    }

    public function test_handle_creates_user_when_exists_in_system()
    {
        Event::fake();

        $data = [
            'email' => 'test@example.com',
            'role' => 'admin',
            'business_id' => 123,
            'created_by' => 1,
        ];

        $systemUser = new \Core\User\Domain\Entities\User(
            id: 1,
            email: 'test@example.com',
            role: null,
            business_id: null
        );

        $this->serviceMock
            ->shouldReceive('getByEmail')
            ->once()
            ->andReturn(null);

        $this->serviceMock
            ->shouldReceive('findByEmailOnSystem')
            ->once()
            ->andReturn($systemUser);

        $this->hooksMock->shouldReceive('dispatch')
            ->twice()
            ->with(Mockery::type(HookContext::class))
            ->andReturn(
                $data,
                [...$data, ...$systemUser->toArray()]
            );

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        Event::shouldReceive('dispatch')->once();

        $result = $this->useCase->handle($data);

        $this->assertIsArray($result);
        $this->assertSame('test@example.com', $result['email']);
    }


    public function test_handle_throws_exception_when_user_not_exists_in_system()
    {
        $data = [
            'email' => 'test@example.com',
            'user_id' => 1,
            'business_id' => 1,
            'role' => 'admin',
            'id' => 1
        ];

        $this->hooksMock->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data);

        $this->serviceMock->shouldReceive('getByEmail')->andReturn(null);
        $this->serviceMock->shouldReceive('findByEmailOnSystem')->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.not_exists'));

        $this->useCase->handle($data);
    }
}

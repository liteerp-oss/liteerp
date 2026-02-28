<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\User\Application\UseCases\CreateUser;
use Core\User\Domain\Entities\User;
use Core\User\Domain\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

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

    public function test_handle_creates_user_when_exists_in_system()
    {
        $data = [
            'email' => 'test@example.com',
            'user_id' => 1,
            'business_id' => 123,
            'group_id' => 10,
        ];

        $systemUser = new User(
            id: 99,
            email: 'test@example.com',
            lang: null,
            avatar: null
        );

        $this->serviceMock
            ->shouldReceive('findByEmailOnSystem')
            ->once()
            ->andReturn($systemUser);

        $this->hooksMock->shouldReceive('dispatch')
            ->twice()
            ->with(Mockery::type(HookContext::class))
            ->andReturn(
                $data,
                [...$data, ...$systemUser->toArray(), 'account_id' => 99]
            );

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        Event::shouldReceive('dispatch')->once();

        $result = $this->useCase->handle($data);

        $this->assertIsArray($result);
        $this->assertSame('test@example.com', $result['email']);
        $this->assertSame(99, $result['account_id']);
    }

    public function test_handle_throws_exception_when_user_not_exists_in_system()
    {
        $data = [
            'email' => 'test@example.com',
            'user_id' => 1,
            'business_id' => 123,
            'group_id' => 10,
        ];

        $this->hooksMock->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data);

        $this->serviceMock->shouldReceive('findByEmailOnSystem')->once()->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.not_exists'));

        $this->useCase->handle($data);
    }
}

<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\User\Application\UseCases\UpdateUser;
use Core\User\Domain\Entities\User;
use Core\User\Domain\Services\UserService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;

class UpdateUserTest extends TestCase
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
        $this->useCase = new UpdateUser($this->serviceMock, $this->hooksMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_throws_exception_when_user_change_role_theyself_in_business()
    {
        $data = [
            'id' => 1,
            'email' => 'test@example.com',
            'role' => 'admin',
            'business_id' => 123,
            'user_id' => 1
        ];

        // UseCase UpdateUser sẽ gọi getByEmail($data)
        $this->serviceMock
            ->shouldReceive('getByEmail')
            ->once()
            ->andReturn(
                User::fromArray($data)
            );
        $this->hooksMock->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.cannot_change_own_role'));

        $this->useCase->handle($data);
    }

    public function test_handle_throws_exception_when_user_exists_in_business()
    {
        $data = [
            'id' => 1,
            'email' => 'test@example.com',
            'role' => 'admin',
            'business_id' => 123,
            'user_id' => 2,
            'lang' => 'en'
        ];

        $this->serviceMock
            ->shouldReceive('getByEmail')
            ->once()
            ->andReturn(User::fromArray($data));
        $this->hooksMock->shouldReceive('dispatch')
            ->twice()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data, $data);

        $result = $this->useCase->handle($data);
        $this->assertIsArray($result);
    }


    public function test_handle_throws_exception_when_updating_self()
    {
        $data = [
            'email' => 'test@example.com',
            'user_id' => 1,
            'business_id' => 123,
            'role' => 'admin',
            'id' => 1,
            'lang' => 'en'
        ];
        $user = new User(1, 'test@example.com', 'admin', 123);

        $this->hooksMock->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data);
        $this->serviceMock->shouldReceive('getByEmail')->andReturn($user);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.cannot_change_own_role'));

        $this->useCase->handle($data);
    }

    public function test_handle_updates_user()
    {
        $data = [
            'email' => 'test@example.com',
            'user_id' => 1,
            'business_id' => 123,
            'role' => 'admin',
            'id' => 1
        ];
        $user = new User(2, 'test@example.com', 'admin', 123);

        $this->hooksMock->shouldReceive('dispatch')
            ->twice()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data, [...$data, ...$user->toArray()]);
        $this->serviceMock->shouldReceive('getByEmail')->andReturn($user);

        DB::shouldReceive('beginTransaction')->once();
        Event::shouldReceive('dispatch')->with('erp.user.update', Mockery::on(function ($payload) {
            return is_array($payload)
                && ($payload['id'] ?? null) === 2
                && ($payload['role_user_id'] ?? null) === 2
                && ($payload['business_id'] ?? null) === 123;
        }))->once();
        DB::shouldReceive('commit')->once();

        $result = $this->useCase->handle($data);

        $this->assertIsArray($result);
        $this->assertSame(2, $result['id']);
    }
}

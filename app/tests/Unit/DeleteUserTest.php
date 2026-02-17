<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\User\Application\UseCases\DeleteUser;
use Core\User\Domain\Entities\User;
use Core\User\Domain\Services\UserService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;

class DeleteUserTest extends TestCase
{
    protected $serviceMock;
    protected $hooksMock;
    protected $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceMock = Mockery::mock(UserService::class);
        $this->hooksMock = Mockery::mock(HookDispatcher::class);
        $this->useCase = new DeleteUser($this->serviceMock, $this->hooksMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_throws_exception_when_user_not_exists()
    {
        $data = ['user_id' => 1, 'business_id' => 1, 'id' => 5];
        $this->hooksMock->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data);
        $this->serviceMock->shouldReceive('findById')->andThrow(new BadException(__('user::messages.not_found')));

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.not_found'));

        $this->useCase->handle($data);
    }

    public function test_handle_throws_exception_when_deleting_self()
    {
        $data = ['user_id' => 1, 'business_id' => 123, 'id' => 1];
        $user = new User(1, 'test@example.com', 'admin', 123);

        $this->hooksMock->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data);
        $this->serviceMock->shouldReceive('findById')->andReturn($user);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.cannot_delete_self'));

        $this->useCase->handle($data);
    }

    public function test_handle_deletes_user()
    {
        $data = ['user_id' => 1, 'business_id' => 123, 'id' => 2];
        $user = new User(2, 'test@example.com', 'admin', 123);

        $afterData = [...$data, ...$user->toArray()];
        $this->hooksMock->shouldReceive('dispatch')
            ->twice()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data, $afterData);
        $this->serviceMock->shouldReceive('findById')->andReturn($user);

        DB::shouldReceive('beginTransaction')->once();
        Event::shouldReceive('dispatch')->with('erp.user.delete', Mockery::on(function ($payload) {
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

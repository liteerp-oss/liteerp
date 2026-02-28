<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\User\Application\UseCases\DeleteUser;
use Core\User\Domain\Entities\User;
use Core\User\Domain\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

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
        $data = ['user_id' => 1, 'business_id' => 1, 'group_id' => 10, 'id' => 5];

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
        $data = ['user_id' => 1, 'business_id' => 123, 'group_id' => 10, 'id' => 1];
        $user = new User(id: 1, email: 'test@example.com', lang: 'en', avatar: null);

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
        $data = ['user_id' => 1, 'business_id' => 123, 'group_id' => 10, 'id' => 2];
        $user = new User(id: 2, email: 'test@example.com', lang: 'en', avatar: null);

        $this->hooksMock->shouldReceive('dispatch')
            ->twice()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data, [...$data, ...$user->toArray(), 'account_id' => 2]);

        $this->serviceMock->shouldReceive('findById')->andReturn($user);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        Event::shouldReceive('dispatch')->with('erp.user.delete', Mockery::on(function ($payload) {
            return is_array($payload)
                && ($payload['id'] ?? null) === 2
                && ($payload['account_id'] ?? null) === 2;
        }))->once();

        $result = $this->useCase->handle($data);

        $this->assertIsArray($result);
        $this->assertSame(2, $result['id']);
        $this->assertSame(2, $result['account_id']);
    }
}

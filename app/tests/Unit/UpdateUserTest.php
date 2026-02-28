<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use App\Supports\Hooks\HookContext;
use App\Supports\Hooks\HookDispatcher;
use Core\User\Application\UseCases\UpdateUser;
use Core\User\Domain\Entities\User;
use Core\User\Domain\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

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

    public function test_handle_throws_exception_when_user_not_exists_on_business()
    {
        $data = [
            'id' => 1,
            'email' => 'test@example.com',
            'business_id' => 123,
            'group_id' => 10,
            'user_id' => 2,
        ];

        $this->hooksMock->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data);

        $this->serviceMock->shouldReceive('getByEmail')->once()->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.not_exists_on_business'));

        $this->useCase->handle($data);
    }

    public function test_handle_throws_exception_when_updating_self()
    {
        $data = [
            'id' => 1,
            'email' => 'test@example.com',
            'business_id' => 123,
            'group_id' => 10,
            'user_id' => 1,
        ];

        $account = new User(id: 1, email: 'test@example.com', lang: 'en', avatar: null);

        $this->hooksMock->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data);

        $this->serviceMock->shouldReceive('getByEmail')->once()->andReturn($account);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.cannot_change_own_role'));

        $this->useCase->handle($data);
    }

    public function test_handle_updates_user()
    {
        $data = [
            'id' => 1,
            'email' => 'test@example.com',
            'business_id' => 123,
            'group_id' => 10,
            'user_id' => 1,
        ];

        $account = new User(id: 2, email: 'test@example.com', lang: 'en', avatar: null);

        $this->hooksMock->shouldReceive('dispatch')
            ->twice()
            ->with(Mockery::type(HookContext::class))
            ->andReturn($data, [...$data, ...$account->toArray(), 'account_id' => 2]);

        $this->serviceMock->shouldReceive('getByEmail')->once()->andReturn($account);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        Event::shouldReceive('dispatch')->with('erp.user.update', Mockery::on(function ($payload) {
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

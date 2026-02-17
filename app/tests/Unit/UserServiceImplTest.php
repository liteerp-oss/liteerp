<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use Core\User\Domain\Entities\User;
use Core\User\Domain\Repositories\UserRepositoryInterface;
use Core\User\Infrastructure\Services\UserServiceImpl;
use Tests\TestCase;
use Mockery;

class UserServiceImplTest extends TestCase
{
    protected $repoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoMock = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new UserServiceImpl($this->repoMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_find_by_id_returns_user()
    {
        $user = new User(1, 'test@example.com', 'admin', 123);
        $this->repoMock->shouldReceive('findById')->with(['id' => 1, 'business_id' => 123])->andReturn($user);

        $result = $this->service->findById(['id' => 1, 'business_id' => 123]);

        $this->assertEquals($user, $result);
    }

    public function test_find_by_id_throws_exception_when_not_found()
    {
        $this->repoMock->shouldReceive('findById')->with(['id' => 1, 'business_id' => 123])->andReturn(null);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.not_found'));

        $this->service->findById(['id' => 1, 'business_id' => 123]);
    }

    public function test_get_by_email_returns_user()
    {
        $user = new User(1, 'test@example.com', 'admin', 123);
        $this->repoMock->shouldReceive('findByEmail')->with(['email' => 'test@example.com', 'business_id' => 123])->andReturn($user);

        $result = $this->service->getByEmail(['email' => 'test@example.com', 'business_id' => 123]);

        $this->assertEquals($user, $result);
    }

    public function test_find_by_email_on_system_returns_user()
    {
        $user = new User(1, 'test@example.com', null, null);
        $this->repoMock->shouldReceive('findByEmailOnSystem')->with(['email' => 'test@example.com'])->andReturn($user);

        $result = $this->service->findByEmailOnSystem(['email' => 'test@example.com']);

        $this->assertEquals($user, $result);
    }
}

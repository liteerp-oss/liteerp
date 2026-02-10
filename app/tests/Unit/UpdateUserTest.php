<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use Core\User\Application\DTOs\CreateUserRequest;
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
    protected $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->serviceMock = Mockery::mock(UserService::class);
        $this->useCase = new UpdateUser($this->serviceMock);
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

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__("You can not change role your-self"));

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

        $result = $this->useCase->handle($data);
        $this->assertInstanceOf(User::class, $result);
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

        $this->serviceMock->shouldReceive('getByEmail')->andReturn($user);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('You can not change role your-self'));

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

        $this->serviceMock->shouldReceive('getByEmail')->andReturn($user);

        DB::shouldReceive('beginTransaction')->once();
        Event::shouldReceive('dispatch')->with('erp.user.update', [
            'id' => 2,
            'email' => 'test@example.com',
            'role' => 'admin',
            'business_id' => 123,
            'role_user_id' => 2,
            'user_id' => 1,
            'business_id' => 123,
            'role' => 'admin',
            'lang' => null,
            'avatar' => null
        ])->once();
        DB::shouldReceive('commit')->once();

        $result = $this->useCase->handle($data);

        $this->assertEquals($user, $result);
    }
}

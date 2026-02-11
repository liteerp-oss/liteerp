<?php

namespace Tests\Unit;

use App\Exceptions\BadException;
use Core\User\Application\DTOs\DeleteUserRequest;
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
    protected $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceMock = Mockery::mock(UserService::class);
        $this->useCase = new DeleteUser($this->serviceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_throws_exception_when_user_not_exists()
    {
        $dto = new DeleteUserRequest(1, 1, 5);

        $this->serviceMock->shouldReceive('findById')->andThrow(new BadException(__('user::messages.not_found')));

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.not_found'));

        $this->useCase->handle([
            ...$dto->toArray(),
            'user_id' => $dto->created_by,
        ]);
    }

    public function test_handle_throws_exception_when_deleting_self()
    {
        $dto = new DeleteUserRequest(1, 123, 1);
        $user = new User(1, 'test@example.com', 'admin', 123);

        $this->serviceMock->shouldReceive('findById')->andReturn($user);

        $this->expectException(BadException::class);
        $this->expectExceptionMessage(__('user::messages.cannot_delete_self'));

        $this->useCase->handle([
            ...$dto->toArray(),
            'user_id' => $dto->created_by,
        ]);
    }

    public function test_handle_deletes_user()
    {
        $dto = new DeleteUserRequest(1, 123, 2);
        $user = new User(2, 'test@example.com', 'admin', 123);

        $this->serviceMock->shouldReceive('findById')->andReturn($user);

        DB::shouldReceive('beginTransaction')->once();
        Event::shouldReceive('dispatch')->with('erp.user.delete', [
            'id' => 2,
            'email' => 'test@example.com',
            'role' => 'admin',
            'business_id' => 123,
            'role_user_id' => 2,
            'user_id' => 1,
            'business_id' => 123,
            'lang' => null,
            'avatar' => null 
        ])->once();
        DB::shouldReceive('commit')->once();

        $result = $this->useCase->handle([
            ...$dto->toArray(),
            'user_id' => $dto->created_by,
        ]);

        $this->assertEquals($user, $result);
    }
    
}

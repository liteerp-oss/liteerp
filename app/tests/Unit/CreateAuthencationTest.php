<?php

namespace Tests\Unit;

use App\Supports\Permissions\Enums\Permission;
use Core\Authencation\Application\DTOs\CreateAuthencationRequest;
use Core\Authencation\Application\UseCases\CreateAuthencation;
use Core\Authencation\Domain\Entities\Authencation;
use Core\Authencation\Domain\Services\AuthencationService;
use Core\AppToken\Application\UseCases\CreateAppToken;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class CreateAuthencationTest extends TestCase
{
    protected $serviceMock;
    protected $createAppTokenMock;
    protected $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->serviceMock = Mockery::mock(AuthencationService::class);
        $this->createAppTokenMock = Mockery::mock(CreateAppToken::class);
        $this->useCase = new CreateAuthencation($this->serviceMock, $this->createAppTokenMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_creates_authencation_successfully()
    {
        $dto = new CreateAuthencationRequest(
            email: 'test@example.com',
            password: 'password123',
            name: 'Test User'
        );

        $account = new Authencation(
            email: 'test@example.com',
            password: Hash::make('password123'),
            name: 'Test User',
            id: 1
        );

        $token = 'some_token';

        $this->serviceMock
            ->shouldReceive('create')
            ->once()
            ->andReturn($account);

        $this->createAppTokenMock
            ->shouldReceive('handle')
            ->once()
            ->andReturn($token);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        Event::shouldReceive('dispatch')
            ->once()
            ->with(Permission::NOTIFICATION_CREATE->value, [
                'user_id' => 1,
                'message' => 'authencation::messages.message_verify_account',
                'title' => 'authencation::messages.subject_verify_account',
                'entity_type' => "users",
                'entity_id' => 1,
                'chanels' => ['mail'],
                'link' => URL::to('/dashboard/verify-account?token=' . $token)
            ]);

        $result = $this->useCase->handle($dto);

        $this->assertInstanceOf(Authencation::class, $result);
        $this->assertEquals(1, $result->id);
    }
}

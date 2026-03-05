<?php

namespace Tests\Unit;

use App\Exceptions\UnauthorizedException;
use App\Supports\Permissions\Enums\Permission;
use Core\Authencation\Application\DTOs\CreateAuthencationRequest;
use Core\Authencation\Application\UseCases\LoginAuthencation;
use Core\Authencation\Domain\Entities\Authencation;
use Core\Authencation\Domain\Services\AuthencationService;
use Core\AppToken\Application\UseCases\CreateAppToken;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

class LoginAuthencationTest extends TestCase
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
        $this->useCase = new LoginAuthencation($this->serviceMock, $this->createAppTokenMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_logs_in_when_email_verified()
    {
        $dto = new CreateAuthencationRequest(
            email: 'test@example.com',
            password: 'password123'
        );

        $account = new Authencation(
            email: 'test@example.com',
            name: 'Test User',
            id: 1,
            email_verified_at: '2023-01-01 00:00:00'
        );

        $webToken = 'web_token';

        $this->serviceMock
            ->shouldReceive('login')
            ->once()
            ->with($dto->toArray())
            ->andReturn($account);

        $this->createAppTokenMock
            ->shouldReceive('handle')
            ->once()
            ->andReturn($webToken);

        $result = $this->useCase->handle($dto);

        $this->assertIsArray($result);
        $this->assertEquals('test@example.com', $result['email']);
        $this->assertEquals('web_token', $result['web_token']);
    }

    public function test_handle_throws_exception_when_email_not_verified()
    {
        $dto = new CreateAuthencationRequest(
            email: 'test@example.com',
            password: 'password123'
        );

        $account = new Authencation(
            email: 'test@example.com',
            name: 'Test User',
            id: 1,
            email_verified_at: null
        );

        $token = 'verification_token';

        $this->serviceMock
            ->shouldReceive('login')
            ->once()
            ->with($dto->toArray())
            ->andReturn($account);

        $this->createAppTokenMock
            ->shouldReceive('handle')
            ->once()
            ->andReturn($token);

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

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage(__("authencation::messages.not_verify"));

        $this->useCase->handle($dto);
    }
}

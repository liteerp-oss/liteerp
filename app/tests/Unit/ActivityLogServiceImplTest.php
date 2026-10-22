<?php

namespace Tests\Unit;

use Core\ActivityLog\Domain\Entities\ActivityLog;
use Core\ActivityLog\Domain\Repositories\ActivityLogRepositoryInterface;
use Core\ActivityLog\Infrastructure\Services\ActivityLogServiceImpl;
use Tests\TestCase;
use Mockery;

class ActivityLogServiceImplTest extends TestCase
{
    protected $repoMock;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repoMock = Mockery::mock(ActivityLogRepositoryInterface::class);
        $this->service = new ActivityLogServiceImpl($this->repoMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_returns_activity_log()
    {
        $data = [
            'user_id' => 1,
            'action' => 'create',
            'description' => 'Created a new customer',
            'entity_type' => 'Customer',
            'entity_id' => 123,
            'business_id' => 1
        ];

        $activityLog = ActivityLog::fromArray($data);
        $activityLog->id = 1;

        $this->repoMock->shouldReceive('getByEntityTypeAndEntityId')
            ->once()
            ->with('Customer', '123')
            ->andReturnNull();

        $this->repoMock->shouldReceive('create')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof ActivityLog &&
                       $arg->user_id === 1 &&
                       $arg->action === 'create' &&
                       $arg->entity_type === 'Customer';
            }))
            ->andReturn($activityLog);

        $result = $this->service->create($data);

        $this->assertInstanceOf(ActivityLog::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('create', $result->action);
        $this->assertEquals('Customer', $result->entity_type);
    }

    public function test_index_returns_array()
    {
        $data = ['business_id' => 1, 'limit' => 10];
        $expectedResult = [
            ['id' => 1, 'action' => 'create', 'entity_type' => 'Customer'],
            ['id' => 2, 'action' => 'update', 'entity_type' => 'Product']
        ];

        $this->repoMock->shouldReceive('index')
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->service->index($data);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('create', $result[0]['action']);
        $this->assertEquals('Customer', $result[0]['entity_type']);
    }
}
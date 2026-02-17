<?php

namespace Tests\Unit;

use Core\User\Application\DTOs\IndexUserRequest;
use Tests\TestCase;

class IndexUserTest extends TestCase
{
    public function test_from_array_creates_dto_correctly()
    {
        $dto = IndexUserRequest::fromArray([
            'user_id' => 1,
            'business_id' => 123,
            'keywords' => 'john',
            'order_by' => 'ASC',
        ]);

        $this->assertSame(1, $dto->created_by);
        $this->assertSame(123, $dto->business_id);
        $this->assertSame('john', $dto->keywords);
        $this->assertSame('ASC', $dto->order_by);
    }

    public function test_from_array_uses_default_order_by()
    {
        $dto = IndexUserRequest::fromArray([
            'user_id' => 1,
            'business_id' => 123,
        ]);

        $this->assertSame('DESC', $dto->order_by);
    }
}

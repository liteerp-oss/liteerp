<?php

namespace Tests\Unit;

use Core\User\Application\DTOs\CreateUserRequest;
use Tests\TestCase;

class CreateUserRequestTest extends TestCase
{
    public function test_from_array_creates_dto_correctly()
    {
        $data = [
            'email' => 'test@example.com',
            'user_id' => 1,
            'business_id' => 123,
            'group_id' => 10,
            'id' => 2
        ];

        $dto = CreateUserRequest::fromArray($data);

        $this->assertEquals('test@example.com', $dto->email);
        $this->assertEquals(1, $dto->created_by);
        $this->assertEquals(123, $dto->business_id);
        $this->assertEquals(10, $dto->group_id);
        $this->assertEquals(2, $dto->id);
    }

    public function test_from_array_with_optional_id()
    {
        $data = [
            'email' => 'test@example.com',
            'user_id' => 1,
            'business_id' => 123,
            'group_id' => 10,
        ];

        $dto = CreateUserRequest::fromArray($data);

        $this->assertEquals('test@example.com', $dto->email);
        $this->assertEquals(1, $dto->created_by);
        $this->assertEquals(123, $dto->business_id);
        $this->assertEquals(10, $dto->group_id);
        $this->assertNull($dto->id);
    }

    public function test_to_array_converts_correctly()
    {
        $dto = new CreateUserRequest('test@example.com', 1, 123, 10, 2);

        $array = $dto->toArray();

        $expected = [
            'email' => 'test@example.com',
            'created_by' => 1,
            'business_id' => 123,
            'group_id' => 10,
            'id' => 2
        ];

        $this->assertEquals($expected, $array);
    }
}

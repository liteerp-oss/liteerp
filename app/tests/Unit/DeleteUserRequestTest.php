<?php

namespace Tests\Unit;

use Core\User\Application\DTOs\DeleteUserRequest;
use Tests\TestCase;

class DeleteUserRequestTest extends TestCase
{
    public function test_from_array_creates_dto_correctly()
    {
        $data = [
            'user_id' => 1,
            'business_id' => 123,
            'id' => 2,
            'group_id' => 10,
        ];

        $dto = DeleteUserRequest::fromArray($data);

        $this->assertEquals(1, $dto->created_by);
        $this->assertEquals(123, $dto->business_id);
        $this->assertEquals(2, $dto->id);
        $this->assertEquals(10, $dto->group_id);
    }

    public function test_from_array_requires_group_id()
    {
        $data = [
            'user_id' => 1,
            'business_id' => 123,
            'id' => 2,
            'group_id' => 10,
        ];

        $dto = DeleteUserRequest::fromArray($data);

        $this->assertEquals(1, $dto->created_by);
        $this->assertEquals(123, $dto->business_id);
        $this->assertEquals(2, $dto->id);
        $this->assertEquals(10, $dto->group_id);
    }

    public function test_to_array_converts_correctly()
    {
        $dto = new DeleteUserRequest(1, 123, 2, 10);

        $array = $dto->toArray();

        $expected = [
            'created_by' => 1,
            'business_id' => 123,
            'id' => 2,
            'group_id' => 10
        ];

        $this->assertEquals($expected, $array);
    }
}

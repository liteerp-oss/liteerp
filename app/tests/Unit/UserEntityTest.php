<?php

namespace Tests\Unit;

use Core\User\Domain\Entities\User;
use Tests\TestCase;

class UserEntityTest extends TestCase
{
    public function test_from_array_creates_user_correctly()
    {
        $data = [
            'id' => 1,
            'email' => 'test@example.com',
            'lang' => 'en',
            'avatar' => 'avatar.png',
        ];

        $user = User::fromArray($data);

        $this->assertEquals(1, $user->id);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('en', $user->lang);
        $this->assertEquals('avatar.png', $user->avatar);
    }

    public function test_from_array_with_null_values()
    {
        $data = [
            'email' => 'test@example.com'
        ];

        $user = User::fromArray($data);

        $this->assertNull($user->id);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertNull($user->lang);
        $this->assertNull($user->avatar);
    }

    public function test_to_array_converts_correctly()
    {
        $user = new User(
            id: 1,
            email: 'test@example.com',
            lang: 'en',
            avatar: null
        );

        $array = $user->toArray();

        $expected = [
            'id' => 1,
            'email' => 'test@example.com',
            'lang' => 'en',
            'avatar' => null
        ];

        $this->assertEquals($expected, $array);
    }
}

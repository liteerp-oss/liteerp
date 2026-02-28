<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private function createValidGroupId(): int
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $businessId = DB::table('business')->insertGetId([
            'name' => 'Test Business',
            'address' => 'HCM',
            'tax_code' => null,
            'phone' => null,
            'email' => null,
            'logo_url' => null,
            'bank_name' => null,
            'bank_account_number' => null,
            'bank_account_name' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('permission_groups')->insertGetId([
            'name' => 'group-' . uniqid(),
            'type' => 'system',
            'user_id' => $userId,
            'business_id' => $businessId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_index_users()
    {
        Event::fake();
        $this->withoutMiddleware();
        // Assuming route is defined
        $response = $this->get('/api/business-access/users?business_id=1&user_id=1');
        
        $response->assertStatus(200);
    }

    public function test_create_user_valid_data()
    {
        Event::fake();
        $this->withoutMiddleware();
        $groupId = $this->createValidGroupId();
        $data = [
            'email' => 'test@example.com',
            'role' => 'admin',
            'business_id' => 1,
            'user_id' => 1,
            'group_id' => $groupId,
            'name' => 'a',
            'password' => 'adfadf'
        ];

        $response = $this->post('/api/business-access/users', $data);

        $response->assertStatus(400);
    }

    public function test_create_user_invalid_email()
    {
        Event::fake();
        $this->withoutMiddleware();
        $data = [
            'email' => 'invalid-email',
            'role' => 'admin',
            'business_id' => 1,
            'user_id' => 1
        ];

        $response = $this->post('/api/business-access/users', $data);

        $response->assertStatus(302);
    }

    public function test_create_user_invalid_role()
    {
        Event::fake();
        $this->withoutMiddleware();
        $data = [
            'email' => 'test@example.com',
            'role' => 'invalid-role',
            'business_id' => 1,
            'user_id' => 1
        ];

        $response = $this->post('/api/business-access/users', $data);

        $response->assertStatus(302);
    }

    public function test_update_user_invalid()
    {
        Event::fake();
        $this->withoutMiddleware();
        $groupId = $this->createValidGroupId();
        $data = [
            'email' => 'test@example.com',
            'role' => 'manager',
            'business_id' => 1,
            'user_id' => 1,
            'group_id' => $groupId,
        ];

        $response = $this->put('/api/business-access/users/1', $data);

        $response->assertStatus(500);
    }

    public function test_delete_user_invalid()
    {
        Event::fake();
        $this->withoutMiddleware();
        $groupId = $this->createValidGroupId();
        $response = $this->delete('/api/business-access/users/1?business_id=1&user_id=1&group_id=' . $groupId);

        $response->assertStatus(400);
    }
}

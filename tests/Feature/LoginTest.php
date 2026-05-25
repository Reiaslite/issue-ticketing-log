<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure a valid username/password returns a token and user payload.
     */
    public function test_login_returns_access_token_and_user_payload(): void
    {
        $user = User::factory()->create([
            'username' => 'test.user',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'username' => 'test.user',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['access_token', 'token_type', 'expires_in', 'user'],
            ])
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonPath('data.user.username', $user->username);
    }

    /**
     * Ensure invalid credentials return the contract error payload.
     */
    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'username' => 'test.user',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'username' => 'test.user',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid username or password',
                'errors' => null,
            ]);
    }
}

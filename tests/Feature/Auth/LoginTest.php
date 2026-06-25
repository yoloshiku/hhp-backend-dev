<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Class LoginTest
 *
 * Feature tests for the user login endpoint.
 *
 * This test suite verifies the behavior of:
 * POST /api/auth/login
 *
 * Covered scenarios:
 * - Successful login
 * - Invalid credentials
 * - Non-existing user
 * - Validation errors
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can login successfully.
     *
     * @return void
     */
    public function testUserCanLoginSuccessfully(): void
    {
        $user = User::factory()->create([
            'email' => 'marlon@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'marlon@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user',
                    'token'
                ]
            ]);
    }

    /**
     * Test login fails with incorrect password.
     *
     * @return void
     */
    public function testLoginFailsWithWrongPassword(): void
    {
        User::factory()->create([
            'email' => 'marlon@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'marlon@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error.',
            ])
            ->assertJsonStructure([
                'errors' => [
                    'message',
                ]
            ]);
    }

    /**
     * Test login fails when user does not exist.
     *
     * @return void
     */
    public function testLoginFailsWithNonExistingEmail(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'notfound@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error.',
            ])
            ->assertJsonStructure([
                'errors' => [
                    'message',
                ]
            ]);
    }

    /**
     * Test validation errors when fields are missing.
     *
     * @return void
     */
    public function testLoginValidationErrors(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error.',
            ])
            ->assertJsonStructure([
                'errors' => [
                    'email',
                    'password',
                ]
            ]);
    }
}
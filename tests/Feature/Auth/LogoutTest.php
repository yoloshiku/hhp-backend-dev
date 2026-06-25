<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

/**
 * Class LogoutTest
 *
 * Feature tests for the AuthController->logout.
 *
 * This test suite verifies the behavior of the authenticated user's
 * logout endpoint (`/api/auth/logout`) under different scenarios.
 *
 * Covered scenarios:
 * - Successful logout for an authenticated user
 * - Unauthorized logout when no user is authenticated
 *
 * Testing strategy:
 * - Uses Laravel's HTTP testing utilities (postJson)
 * - Uses Sanctum authentication via withAccessToken($token)
 * - Uses RefreshDatabase to ensure a clean database state per test
 * - Executes the full application flow (controller → service → repository)
 *
 * Notes:
 * - No mocking is used; this is a full feature test
 * - Ensures both API response and database state are validated
 *
 * @package Tests\Feature\Auth
 */
class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful logout for an authenticated user.
     *
     * Verifies that:
     * - Successful response after Logged out
     * - Token is deleted
     *
     * @return void
     */
    public function testLogoutSuccessfullyAndTokenIsDeleted(): void
    {
        $user = User::factory()->create();

        // Create token
        $token = $user->createToken('test-token')->plainTextToken;;
        
        // Properly set current token
        $user->withAccessToken($token);

        // Assert that there is a register in the database in personal_access_tokens table
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                    ->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Logged out successfully'
                 ]);

        // Assert that there is not a register in the database in personal_access_tokens table
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /**
     * Test logout fails
     *
     * Verifies that:
     * - Logout fails when user is not authenticated
     * - Shows message when user is not authenticated
     *
     * @return void
     */
    public function testLogoutFailsWhenUserIsNotAuthenticated(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401)
                 ->assertJson([
                    'success' => false,
                    'message' => 'You must be logged in.'
                ]);
    }    
    
}

<?php

namespace Tests\Feature\User\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class ProfileControllerTest
 *
 * Feature tests for the ProfileController.
 *
 * This test suite verifies the behavior of the authenticated user's
 * profile update endpoint (`/api/user/profile`) under different scenarios.
 *
 * Covered scenarios:
 * - Successful profile update for an authenticated and verified user
 * - Unauthorized access when no user is authenticated
 * - Forbidden access when the user's email is not verified
 * - Validation errors when required fields are missing or invalid
 *
 * Testing strategy:
 * - Uses Laravel's HTTP testing utilities (putJson)
 * - Uses Sanctum authentication via actingAs()
 * - Uses RefreshDatabase to ensure a clean database state per test
 * - Executes the full application flow (controller → service → repository)
 *
 * Notes:
 * - No mocking is used; this is a full feature test
 * - Email verification is simulated via the `email_verified_at` field
 * - Ensures both API response and database state are validated
 *
 * @package Tests\Feature\User\Controllers
 */
class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful profile update for an authenticated and verified user.
     *
     * Verifies that:
     * - The request is authorized via Sanctum
     * - The user's profile data is updated successfully
     * - A successful JSON response is returned
     * - The database reflects the updated user information
     *
     * @return void
     */
    public function testUpdateProfileSuccessfully(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // IMPORTANT
        ]);

        $data = [
            'first_name' => 'Updated',
            'last_name' => 'User',
            'password' => '12345678',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/user/profile', $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated',
        ]);
    }

    /**
     * Test update fails if user is not authenticated.
     */
    public function testUpdateProfileUnauthorized(): void
    {
        $response = $this->putJson('/api/user/profile', []);

        $response->assertStatus(401);
    }

    /**
     * Test update fails if email is not verified.
     */
    public function testUpdateProfileFailsIfNotVerified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null, // NOT verified
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/user/profile', [
                'first_name' => 'Test',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test validation errors.
     */
    public function testUpdateProfileValidationFails(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/user/profile', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
            ]);
    }
}
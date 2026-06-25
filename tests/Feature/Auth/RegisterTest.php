<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmailCustom;

/**
 * Class RegisterTest
 *
 * Feature tests for the user registration endpoint.
 *
 * This test suite verifies the behavior of the API endpoint:
 * POST /api/auth/register
 *
 * Covered scenarios:
 * - Successful user registration
 * - Validation errors when required fields are missing
 * - Email uniqueness constraint
 * - Sending of email verification notification
 *
 * These are Feature tests, meaning they test the full request lifecycle:
 * HTTP request → Controller → Service → Repository → Database → Response
 *
 * Database is refreshed between tests to ensure isolation.
 */
class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can be successfully created.
     *
     * This test verifies:
     * - The API returns a 200 status code
     * - The response contains success = true
     * - The expected JSON structure is returned
     * - The user is persisted in the database
     *
     * @return void
     */
    public function testCreateUser(): void
    {
        $data = [
            'first_name' => 'Marlon Armando',
            'last_name' => 'Meneses Bejarano',
            'email' => 'marlon@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'User registered successfully.'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user',
                    'token'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'marlon@example.com'
        ]);
    }

    /**
     * Test that validation errors are returned when required fields are missing.
     *
     * This test verifies:
     * - The API returns a 422 status code
     * - A validation error response is returned
     * - All required fields are included in the error response
     *
     * @return void
     */
    public function testRegisterWithEmptyFields(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error.'
            ])
            ->assertJsonStructure([
                'errors' => [
                    'first_name',
                    'last_name',
                    'email',
                    'password'
                ]
            ]);
    }

    /**
     * Test that the email must be unique.
     *
     * This test verifies:
     * - A user cannot register with an email that already exists
     * - The API returns a 422 status code
     * - The email field is included in validation errors
     *
     * @return void
     */
    public function testEmailMustBeUnique(): void
    {
        User::factory()->create([
            'email' => 'marlon@example.com'
        ]);

        $data = [
            'first_name' => 'Marlon Armando',
            'last_name' => 'Meneses Bejarano',
            'email' => 'marlon@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that a verification email is sent upon registration.
     *
     * This test verifies:
     * - The notification system is triggered
     * - The VerifyEmailCustom notification is sent to the newly created user
     *
     * Notification::fake() is used to prevent actual email sending
     * and to assert that the notification was dispatched.
     *
     * @return void
     */
    public function testVerificationEmailIsSent(): void
    {
        Notification::fake();

        $data = [
            'first_name' => 'Marlon Armando',
            'last_name' => 'Meneses Bejarano',
            'email' => 'marlon@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->postJson('/api/auth/register', $data);

        Notification::assertSentTo(
            User::first(),
            VerifyEmailCustom::class
        );
    }
}
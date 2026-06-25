<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmailCustom;

/**
 * Class EmailVerificationNotificationTest
 *
 * Feature tests for the email verification notification endpoint.
 *
 * This test suite verifies the behavior of the endpoint responsible for
 * resending email verification notifications (`/api/email/verification-notification`).
 *
 * Covered scenarios:
 * - Successfully sending a verification email to an unverified authenticated user
 * - Rejecting requests from unauthenticated users (401 Unauthorized)
 * - Preventing email resend when the user is already verified
 *
 * Testing strategy:
 * - Uses Laravel's HTTP testing utilities (postJson) to simulate API requests
 * - Uses Sanctum authentication via actingAs() for protected routes
 * - Uses Notification::fake() to prevent real notifications and assert dispatching
 * - Uses RefreshDatabase to ensure a clean database state for each test
 *
 * Notes:
 * - Verifies that the correct notification (VerifyEmailCustom) is sent
 * - Ensures no notification is sent when the user is already verified
 * - Assumes validation or service layer returns HTTP 422 for already verified users
 *
 * @package Tests\Feature\Auth
 */
class EmailVerificationNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a verification email is sent to an authenticated, unverified user.
     *
     * Verifies that:
     * - The request is authenticated via Sanctum
     * - A successful response (200) is returned
     * - The VerifyEmailCustom notification is dispatched to the user
     *
     * @return void
     */
    public function testVerificationEmailCanBeSent(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/email/verification-notification');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Verification email sent.',
            ]);

        Notification::assertSentTo(
            $user,
            VerifyEmailCustom::class
        );
    }

    /**
     * Test Verification email fails if user is unquthenticated
     * and returns status 401
     * @return void
     */
    public function testVerificationEmailFailsIfUnauthenticated(): void
    {
        $response = $this->postJson('/api/email/verification-notification');

        $response->assertStatus(401);
    }

    /**
     * Test Verification email fails if user is already verified
     * and returns status 422
     * @return void
     */
    public function testVerificationEmailFailsIfAlreadyVerified(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/email/verification-notification');

        $response->assertStatus(422);

        Notification::assertNothingSent();
    }
}
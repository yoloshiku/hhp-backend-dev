<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;

/**
 * Class EmailVerificationTest
 *
 * Feature tests for the email verification process.
 *
 * This test suite validates the behavior of the email verification
 * endpoint (`/email/verify/{id}/{hash}`), ensuring that signed URLs,
 * hash validation, and verification logic work as expected.
 *
 * Covered scenarios:
 * - Successful email verification with a valid signed URL and correct hash
 * - Verification failure when the hash is invalid
 * - Verification failure when the URL signature is missing or invalid
 * - Handling of already verified users
 *
 * Testing strategy:
 * - Uses Laravel's URL::temporarySignedRoute to generate valid signed URLs
 * - Uses getJson() to simulate HTTP requests to the verification endpoint
 * - Uses RefreshDatabase to ensure a clean database state for each test
 * - Verifies both HTTP responses and database state changes
 *
 * Notes:
 * - The hash is generated using sha1($user->email) to match Laravel's default behavior
 * - The 'signed' middleware is enforced and tested via valid/invalid URLs
 * - No mocking is used; this is a full feature test of the verification flow
 *
 * @package Tests\Feature\Auth
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can verify their email with a valid signed URL.
     *
     * Verifies that:
     * - The request succeeds with HTTP 200
     * - The correct success message is returned
     * - The user's email_verified_at field is updated
     *
     * @return void
     */
    public function testEmailCanBeVerified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        /**
         * This means:
         * Create a URL that:
         * belongs to a specific route
         * is valid only for a limited time
         * cannot be tampered with
         */
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Email verified successfully.',
            ]);

        /**
         * $user->fresh() means
         * Give me a NEW copy of this model from the database.
         */
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function testEmailVerificationFailsWithInvalidHash(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => 'invalid-hash',
            ]
        );

        $response = $this->getJson($url);

        $response->assertStatus(403);
    }

    public function testEmailVerificationFailsWithInvalidSignature(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $url = route('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        // NOT signed → should fail
        $response = $this->getJson($url);

        $response->assertStatus(403);
    }

    public function testAlreadyVerifiedEmail(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->getJson($url);

        $response->assertStatus(200);
    }
}
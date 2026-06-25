<?php

namespace Tests\Feature\NewsletterSubscriber;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNotificationNewsletterSubscriberMail;
use App\Mail\NewsletterSubscriberMail;

/**
 * Class NewsletterSubscriberControllerTest
 *
 * Feature tests for the NewsletterSubscriberController endpoint.
 *
 * This test suite verifies the behavior of the contact API
 * in a real HTTP context, including request validation,
 * response structure, and email dispatching.
 *
 * Covered scenarios:
 * - Successful newsletter subscriber message submission
 * - Validation errors when required fields are missing
 * - Email notification is sent upon successful submission
 *
 * Testing strategy:
 * - Uses Laravel's HTTP testing helpers (postJson)
 * - Uses Mail::fake() to prevent real email sending
 * - Verifies that the AdminNotificationNewsletterSubscriberMail and NewsletterSubscriberMail 
 * mailable is dispatched
 * - Uses RefreshDatabase to ensure test isolation
 *
 * Notes:
 * - This test does NOT mock the service layer, allowing full
 *   execution of business logic (including email sending)
 * - Ensures end-to-end behavior from controller to service layer
 *
 * @package Tests\Feature\NewsletterSubscriber
 */
class NewsletterSubscriberControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test storing a newsletter subscriber successfully.
     *
     * Verifies:
     * - The endpoint returns HTTP 201
     * - The response contains expected success message
     * - The JSON structure matches the API contract
     * - A AdminNotificationNewsletterSubscriberMail and NewsletterSubscriberMail 
     * mailable is dispatched
     * 
     * Testing strategy:
     * - Uses Mail::fake() to prevent real emails from being sent
     * - Asserts that the expected mailable is triggered
     *
     * @return void
     */
    public function testStoreNewsletterSubscriber(): void
    {
        Mail::fake();

        $data = [
            'email' => 'marlon@example.com',
            'country' => 'Colombia',
        ];

        $response = $this->postJson('/api/newsletter-subscriber', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Newsletter Subscriber registered successfully'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'newsletterSubscriber'
                ]
            ]);

        Mail::assertSent(AdminNotificationNewsletterSubscriberMail::class);
        Mail::assertSent(NewsletterSubscriberMail::class);
    }

    /**
     * Test validation failure when required fields are missing.
     *
     * Verifies:
     * - The endpoint returns HTTP 422
     * - Validation errors are returned for required fields
     * - No service interaction occurs
     *
     * @return void
     */
    public function testStoreNewsletterSubscriberValidationFails(): void
    {
        $response = $this->postJson('/api/newsletter-subscriber', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
                'country'
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
        NewsletterSubscriber::create([
            'email' => 'marlon@example.com',
            'country' => 'Colombia'
        ]);

        $data = [
            'email' => 'marlon@example.com',
            'country' => 'Colombia'
        ];

        $response = $this->postJson('/api/newsletter-subscriber', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }    

}

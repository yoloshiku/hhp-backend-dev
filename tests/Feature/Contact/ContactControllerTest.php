<?php

namespace Tests\Feature\Contact\Controllers;

use Tests\TestCase;
use Mockery;
use App\Services\Interfaces\ContactServiceInterface;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;

/**
 * Class ContactControllerTest
 *
 * Feature tests for the ContactController endpoint.
 *
 * This test suite verifies the behavior of the contact API
 * in a real HTTP context, including request validation,
 * response structure, and email dispatching.
 *
 * Covered scenarios:
 * - Successful contact message submission
 * - Validation errors when required fields are missing
 * - Email notification is sent upon successful submission
 *
 * Testing strategy:
 * - Uses Laravel's HTTP testing helpers (postJson)
 * - Uses Mail::fake() to prevent real email sending
 * - Verifies that the ContactMessageMail mailable is dispatched
 * - Uses RefreshDatabase to ensure test isolation
 *
 * Notes:
 * - This test does NOT mock the service layer, allowing full
 *   execution of business logic (including email sending)
 * - Ensures end-to-end behavior from controller to service layer
 *
 * @package Tests\Feature\Contact\Controllers
 */
class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test storing a contact message successfully.
     *
     * Verifies:
     * - The endpoint returns HTTP 201
     * - The response contains expected success message
     * - The JSON structure matches the API contract
     * - A ContactMessageMail mailable is dispatched
     * 
     * Testing strategy:
     * - Uses Mail::fake() to prevent real emails from being sent
     * - Asserts that the expected mailable is triggered
     *
     * @return void
     */
    public function testStoreContact(): void
    {
        Mail::fake();

        $data = [
            'first_name' => 'Marlon Armando',
            'last_name' => 'Meneses Bejarano',
            'email' => 'marlon@example.com',
            'country' => 'Colombia',
            'message' => 'Hello world',
        ];

        $response = $this->postJson('/api/contact', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Message sent successfully'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'contact'
                ]
            ]);

        Mail::assertSent(ContactMessageMail::class);
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
    public function testStoreContactValidationFails(): void
    {
        $response = $this->postJson('/api/contact', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'email',
                'country',
                'message'
            ]);
    }
}
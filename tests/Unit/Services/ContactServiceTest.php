<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Mockery;
use App\Services\ContactService;
use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;

/**
 * Class ContactServiceTest
 *
 * Unit tests for the ContactService class.
 *
 * This test suite verifies the business logic responsible for
 * handling contact form submissions.
 *
 * Covered scenarios:
 * - Contact data is passed correctly to the repository layer
 * - A Contact model instance is returned after creation
 * - An email notification is dispatched upon successful storage
 *
 * Testing strategy:
 * - The ContactRepositoryInterface is mocked to isolate the service logic
 * - Mail::fake() is used to prevent real emails from being sent
 * - Assertions are performed to verify that the expected mailable is sent
 *
 * Notes:
 * - This is a unit test; no real database interactions occur
 * - Focuses only on service behavior and side effects (email sending)
 *
 * @package Tests\Unit\Services
 */
class ContactServiceTest extends TestCase
{
    /**
     * Mocked repository instance.
     *
     * @var \Mockery\MockInterface|ContactRepositoryInterface
     */
    protected $contactRepository;

    /**
     * Service under test.
     *
     * @var ContactService
     */
    protected ContactService $contactService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contactRepository = Mockery::mock(ContactRepositoryInterface::class);
        $this->contactService = new ContactService($this->contactRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test storing a contact message and sending email notification.
     *
     * Verifies that:
     * - The repository create method is called once with the correct data
     * - A Contact model instance is returned by the service
     * - The returned instance matches the repository result
     * - A ContactMessageMail mailable is dispatched with the expected data
     *
     * Testing strategy:
     * - Uses a mocked ContactRepositoryInterface to isolate service logic
     * - Uses Mail::fake() to prevent real emails and allow assertions
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

        $contact = new Contact($data);

        $this->contactRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($contact);

        $result = $this->contactService->store($data);

        $this->assertInstanceOf(Contact::class, $result);
        $this->assertSame($contact, $result);

        Mail::assertSent(ContactMessageMail::class, function ($mail) use ($data) {
            return $mail->data['email'] === $data['email'];
        });
    }
}
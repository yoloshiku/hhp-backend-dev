<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Mockery;
use App\Services\NewsletterSubscriberService;
use App\Models\NewsletterSubscriber;
use App\Repositories\Interfaces\NewsletterSubscriberRepositoryInterface;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNotificationNewsletterSubscriberMail;
use App\Mail\NewsletterSubscriberMail;

/**
 * Class NewsletterSubscriberServiceTest
 *
 * Unit tests for the NewsletterSubscriberService class.
 *
 * This test suite verifies the business logic responsible for
 * handling newsletter subscriber form submissions.
 *
 * Covered scenarios:
 * - Newsletter subscriber data is passed correctly to the repository layer
 * - A NewsletterSubscriber model instance is returned after creation
 * - An email notification is dispatched upon successful storage
 *
 * Testing strategy:
 * - The NewsletterSubscriberRepositoryInterface is mocked to isolate the service logic
 * - Mail::fake() is used to prevent real emails from being sent
 * - Assertions are performed to verify that the expected mailable is sent
 *
 * Notes:
 * - This is a unit test; no real database interactions occur
 * - Focuses only on service behavior and side effects (email sending)
 *
 * @package Tests\Unit\Services
 */
class NewsletterSubscriberServiceTest extends TestCase
{
    /**
     * Mocked repository instance.
     *
     * @var \Mockery\MockInterface|NewsletterSubscriberRepositoryInterface
     */
    protected $newsletterSubscriberRepository;

    /**
     * Service under test.
     *
     * @var NewsletterSubscriberService
     */
    protected NewsletterSubscriberService $newsletterSubscriberService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->newsletterSubscriberRepository = Mockery::mock(NewsletterSubscriberRepositoryInterface::class);
        $this->newsletterSubscriberService = new NewsletterSubscriberService($this->newsletterSubscriberRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test storing a newsletter subscriber and sending email notifications.
     *
     * Verifies that:
     * - The repository create method is called once with the correct data
     * - A NewsletterSubscriber model instance is returned by the service
     * - The returned instance matches the repository result
     * - A AdminNotificationNewsletterSubscriberMail and NewsletterSubscriberMail
     *  mailable are dispatched with the expected data
     * 
     *
     * Testing strategy:
     * - Uses a mocked NewsletterSubscriberRepositoryInterface to isolate service logic
     * - Uses Mail::fake() to prevent real emails and allow assertions
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

        $newsletterSubscriber = new NewsletterSubscriber($data);

        $this->newsletterSubscriberRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($newsletterSubscriber);

        $result = $this->newsletterSubscriberService->store($data);

        $this->assertInstanceOf(NewsletterSubscriber::class, $result);
        $this->assertSame($newsletterSubscriber, $result);

        Mail::assertSent(AdminNotificationNewsletterSubscriberMail::class, function ($mail) use ($data) {
            return $mail->data['email'] === $data['email'];
        });

        Mail::assertSent(NewsletterSubscriberMail::class, function ($mail) use ($data) {
            return $mail->data['email'] === $data['email'];
        });
    }    
}

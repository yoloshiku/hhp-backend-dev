<?php

namespace Tests\Feature\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\NewsletterSubscriberRepository;
use App\Models\NewsletterSubscriber;

/**
 * Class NewsletterSubscriberRepositoryTest
 *
 * Feature tests for NewsletterSubscriberRepository.
 *
 * This test suite verifies:
 * - Newsletter subscriber records are correctly persisted
 * - Returned model instance is valid
 *
 * Uses real database interactions.
 */
class NewsletterSubscriberRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Repository instance.
     *
     * @var NewsletterSubscriberRepository
     */
    protected NewsletterSubscriberRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new NewsletterSubscriberRepository();
    }

    /**
     * Test creating a newsletter subscriber record.
     *
     * Verifies:
     * - A newsletter subscriber is stored in the database
     * - The returned instance is a NewsletterSubscriber model
     * - Stored data matches input
     */
    public function testCreateNewsletterSubscriber(): void
    {
        $data = [
            'email' => 'marlon@example.com',
            'country' => 'Colombia',
        ];

        $newsletterSubscriber = $this->repository->create($data);

        // Assert instance
        $this->assertInstanceOf(NewsletterSubscriber::class, $newsletterSubscriber);

        // Assert DB
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'marlon@example.com',
            'country' => 'Colombia'
        ]);

        // Optional: assert returned model values
        $this->assertEquals('marlon@example.com', $newsletterSubscriber->email);
    }    
}

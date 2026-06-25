<?php

namespace Tests\Feature\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\ContactRepository;
use App\Models\Contact;

/**
 * Class ContactRepositoryTest
 *
 * Feature tests for ContactRepository.
 *
 * This test suite verifies:
 * - Contact records are correctly persisted
 * - Returned model instance is valid
 *
 * Uses real database interactions.
 */
class ContactRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Repository instance.
     *
     * @var ContactRepository
     */
    protected ContactRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new ContactRepository();
    }

    /**
     * Test creating a contact record.
     *
     * Verifies:
     * - A contact is stored in the database
     * - The returned instance is a Contact model
     * - Stored data matches input
     */
    public function testCreateContact(): void
    {
        $data = [
            'first_name' => 'Marlon Armando',
            'last_name' => 'Meneses Bejarano',
            'email' => 'marlon@example.com',
            'country' => 'Colombia',
            'message' => 'Hello world',
        ];

        $contact = $this->repository->create($data);

        // Assert instance
        $this->assertInstanceOf(Contact::class, $contact);

        // Assert DB
        $this->assertDatabaseHas('contacts', [
            'first_name' => 'Marlon Armando',
            'last_name' => 'Meneses Bejarano',
            'email' => 'marlon@example.com',
            'country' => 'Colombia',
            'message' => 'Hello world',
        ]);

        // Optional: assert returned model values
        $this->assertEquals('Marlon Armando', $contact->first_name);
    }
}
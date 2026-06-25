<?php

namespace Tests\Feature\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Repositories\UserRepository;
use Laravel\Sanctum\Sanctum;

/**
 * Class UserRepositoryTest
 *
 * Feature (integration) tests for the UserRepository.
 *
 * This test suite verifies that the repository correctly interacts
 * with the database through the Eloquent ORM in :contentReference[oaicite:0]{index=0}.
 *
 * Covered responsibilities:
 * - Creating users
 * - Updating user data
 * - Retrieving users by email
 *
 * Notes:
 * - These are NOT pure unit tests; they interact with the database.
 * - The RefreshDatabase trait ensures a clean database state
 *   before each test execution.
 * - No mocking is used, as we are validating real persistence behavior.
 */
class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * UserRepository instance.
     *
     * @var UserRepository
     */
    protected UserRepository $repository;

    /**
     * Set up the test environment.
     *
     * Initializes a new instance of the UserRepository
     * before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new UserRepository();
    }

    /**
     * Test creating a new user.
     *
     * Verifies:
     * - A User instance is returned
     * - The user is persisted in the database
     *
     * @return void
     */
    public function testCreateUser(): void
    {
        $data = [
            'first_name' => 'Marlon',
            'last_name' => 'Meneses',
            'email' => 'marlon@example.com',
            'password' => bcrypt('password'),
        ];

        $user = $this->repository->create($data);

        $this->assertInstanceOf(User::class, $user);

        $this->assertDatabaseHas('users', [
            'email' => 'marlon@example.com',
        ]);
    }

    /**
     * Test updating an existing user.
     *
     * Verifies:
     * - The user's attributes are updated correctly
     * - The changes are persisted in the database
     *
     * @return void
     */
    public function testUpdateUser(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Old Name'
        ]);

        $updatedUser = $this->repository->update($user, [
            'first_name' => 'New Name'
        ]);

        $this->assertEquals('New Name', $updatedUser->first_name);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'New Name'
        ]);
    }

    /**
     * Test finding a user by email.
     *
     * Verifies:
     * - The correct user is returned when the email exists
     * - The returned user matches the expected email
     *
     * @return void
     */
    public function testFindUserByEmail(): void
    {
        User::factory()->create([
            'email' => 'marlon@example.com'
        ]);

        $user = $this->repository->findByEmail('marlon@example.com');

        $this->assertNotNull($user);
        $this->assertEquals('marlon@example.com', $user->email);
    }

    /**
     * Test finding a user by email when it does not exist.
     *
     * Verifies:
     * - Null is returned when no user matches the given email
     *
     * @return void
     */
    public function testFindByEmailReturnsNullIfNotFound(): void
    {
        $user = $this->repository->findByEmail('notfound@example.com');

        $this->assertNull($user);
    }

    /**
     * Test revokeCurrentToken.
     *
     * Verifies:
     * - Current token is deleted
     *
     * @return void
     */
    public function testRevokeCurrentToken(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->accessToken;

        // Properly set current token
        $user->withAccessToken($token);

        // Assert that there is a register in the database in personal_access_tokens table
        $this->assertDatabaseCount('personal_access_tokens', 1);
        
        $result = $this->repository->revokeCurrentToken($user);
        
        $this->assertTrue($result);

        // Assert that there is not a register in the database in personal_access_tokens table
        $this->assertDatabaseCount('personal_access_tokens', 0);        

    }

    /**
     * Test revokeCurrentToken without token.
     *
     * Verifies:
     * - revokeCurrentToken returns false
     *
     * @return void
     */
    public function testRevokeCurrentTokenWithoutToken(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $result = $this->repository->revokeCurrentToken($user);

        $this->assertFalse($result);
    }    
}
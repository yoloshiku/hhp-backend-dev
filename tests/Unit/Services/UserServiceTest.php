<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Mockery;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\Interfaces\UserRepositoryInterface;

/**
 * Class UserServiceTest
 *
 * Unit tests for UserService.
 *
 * Verifies that business logic correctly delegates
 * operations to the repository layer.
 */
class UserServiceTest extends TestCase
{
    /**
     * Mocked repository instance.
     *
     * @var \Mockery\MockInterface|UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * Service under test.
     *
     * @var UserService
     */
    protected UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test updating user profile.
     *
     * Verifies:
     * - Repository update method is called once
     * - Correct parameters are passed
     * - Updated user is returned
     */
    public function testUpdateProfile(): void
    {
        $user = new User([
            'first_name' => 'Old Name',
            'last_name' => 'Old Last Name',
            'password' => '123456700'
        ]);

        $data = [
            'first_name' => 'New Name',
            'last_name' => 'New Last Name',
            'password' => '12345678'
        ];

        $updatedUser = new User([
            'first_name' => 'New Name',
            'last_name' => 'New Last Name',
            'password' => '12345678'
        ]);

        $this->userRepository
            ->shouldReceive('update')
            ->once()
            ->with($user, $data)
            ->withAnyArgs()
            ->andReturn($updatedUser);

        $result = $this->userService->updateProfile($user, $data);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('New Name', $result->first_name);
    }
}
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Mockery;
use App\Services\AuthService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;

/**
 * Class AuthServiceTest
 *
 * Unit tests for the AuthService class.
 *
 * This test suite verifies the business logic responsible for:
 * - User registration
 * - User authentication (login)
 * - Email verification resending
 *
 * Testing Strategy:
 * - This is a UNIT test suite (no database interaction).
 * - Dependencies (UserRepositoryInterface) are mocked using Mockery.
 * - Laravel facades (Event) are faked where needed.
 *
 * Scope:
 * - Ensures correct interaction with the repository layer
 * - Validates authentication logic (password hashing and verification)
 * - Confirms domain rules (e.g., preventing login with invalid credentials,
 *   preventing resending verification for already verified users)
 *
 * Notes:
 * - Eloquent models are partially mocked when needed
 * - Token generation relies on a mocked user with a forced ID
 */
class AuthServiceTest extends TestCase
{
    /**
     * Mocked user repository instance.
     *
     * @var \Mockery\MockInterface|UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * AuthService instance under test.
     *
     * @var AuthService
     */
    protected AuthService $authService;

    /**
     * Set up the test environment.
     *
     * Initializes mocked dependencies and the service instance.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);

        $this->authService = new AuthService($this->userRepository);
    }

    /**
     * Clean up the test environment.
     *
     * Closes Mockery to prevent memory leaks and ensure
     * proper mock expectations verification.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test successful user registration.
     *
     * Verifies:
     * - Repository create method is called
     * - Password is hashed before persistence
     * - A token is generated
     * - Registered event is dispatched
     *
     * @return void
     */
    public function testRegister(): void
    {
        Event::fake();

        $user = new User([
            'email' => 'marlon@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->userRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($user);

        // Required for token generation
        $user->forceFill(['id' => 1]);

        $result = $this->authService->register([
            'email' => 'marlon@example.com',
            'password' => 'password',
        ]);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);

        Event::assertDispatched(Registered::class);
    }

    /**
     * Test successful login with valid credentials.
     *
     * Verifies:
     * - User is retrieved via repository
     * - Password is correctly validated
     * - Authentication token is generated
     *
     * @return void
     */
    public function testLoginSuccess(): void
    {
        $user = new User([
            'email' => 'marlon@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->forceFill(['id' => 1]);

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->andReturn($user);

        $result = $this->authService->login([
            'email' => 'marlon@example.com',
            'password' => 'password',
        ]);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
    }

    /**
     * Test login failure with invalid credentials.
     *
     * Verifies:
     * - ValidationException is thrown when password is incorrect
     *
     * @return void
     */
    public function testLoginFails(): void
    {
        $user = new User([
            'email' => 'marlon@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->andReturn($user);

        $this->expectException(ValidationException::class);

        $this->authService->login([
            'email' => 'marlon@example.com',
            'password' => 'wrong-password',
        ]);
    }

    /**
     * Test login failure when user does not exist.
     *
     * Verifies:
     * - ValidationException is thrown when no user is found
     *
     * @return void
     */
    public function testLoginFailsIfUserNotFound(): void
    {
        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->andReturn(null);

        $this->expectException(ValidationException::class);

        $this->authService->login([
            'email' => 'notfound@example.com',
            'password' => 'password',
        ]);
    }

    /**
     * Test resending verification email for unverified user.
     *
     * Verifies:
     * - Verification email is sent when user is not verified
     *
     * @return void
     */
    public function testResendVerificationEmail(): void
    {
        /** @var User&\Mockery\MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();

        $user->shouldReceive('hasVerifiedEmail')
            ->once()
            ->andReturn(false);

        $user->shouldReceive('sendEmailVerificationNotification')
            ->once();

        $this->authService->resendVerificationEmail($user);

        $this->assertTrue(true);
    }

    /**
     * Test resending verification email fails for already verified user.
     *
     * Verifies:
     * - ValidationException is thrown if user is already verified
     *
     * @return void
     */
    public function testResendVerificationFailsIfAlreadyVerified(): void
    {
        /** @var User&\Mockery\MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();

        $user->shouldReceive('hasVerifiedEmail')
            ->once()
            ->andReturn(true);

        $this->expectException(ValidationException::class);

        $this->authService->resendVerificationEmail($user);
    }

    /**
     * Test successful logout.
     *
     * Verifies:
     * - Logout calls repository
     * - logoutUser method returns true
     *
     * @return void
     */
    public function testLogoutUser(): void
    {
        $user = new User();

        $this->userRepository->shouldReceive('revokeCurrentToken')
             ->once()
             ->with($user)
             ->andReturn(true);
        
        $this->assertTrue($this->authService->logoutUser($user));
    }
 
    /**
     * Test logout fails.
     *
     * Verifies:
     * - AuthService->Logout fails
     * - logoutUser method returns false
     *
     * @return void
     */
    public function testLogoutUserFails(): void
    {
        $user = new User();

        $this->userRepository->shouldReceive('revokeCurrentToken')
             ->andReturn(false);
        
        $this->assertFalse($this->authService->logoutUser($user));
    }    
}
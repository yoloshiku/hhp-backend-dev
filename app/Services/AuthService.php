<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\AuthServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Class AuthService
 *
 * Contains business logic for user authentication.
 */
class AuthService implements AuthServiceInterface
{
    /**
     * @var UserRepositoryInterface
     */    
    protected $userRepository;

    /**
     * AuthService constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user and generate an API token.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        
        $user = $this->userRepository->create($data);

        // Trigger email verification
        event(new Registered($user));

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Authenticate a user using email and password.
     *
     * @param array $credentials
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException If the credentials are invalid
     */    
    public function login(array $data): array
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'message' => ['Invalid credentials.']
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * logout User
     *
     * @param $user
     * @return bool
     */      
    public function logoutUser($user): bool {
        return $this->userRepository->revokeCurrentToken($user);
    }

    /**
     * Resend the email verification notification to the given user.
     *
     * This method checks whether the user's email has already been verified.
     * If the email is already verified, a validation exception is thrown.
     * Otherwise, a new verification email is sent.
     *
     * @param User $user The user instance to send the verification email to
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException If the user's email is already verified
     */
    public function resendVerificationEmail(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Email is already verified.']
            ]);
        }

        $user->sendEmailVerificationNotification();
    }    
}
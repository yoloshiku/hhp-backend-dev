<?php

namespace App\Services\Interfaces;

use App\Models\User;

/**
 * Interface AuthServiceInterface
 *
 * Defines authentication business logic operations.
 */
interface AuthServiceInterface
{
    /**
     * Register a new user and generate an auth token.
     *
     * @param array $data
     * @return array
     */    
    public function register(array $data): array;
    
    /**
     * Authenticate a user and generate a token.
     *
     * @param array $credentials
     * @return array
     */
    public function login(array $data): array;
    
    /**
     * Resend verification email to the user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function resendVerificationEmail(User $user): void;   

    /**
     * logout User
     *
     * @param $user
     * @return bool
     */    
    public function logoutUser($user): bool;
}
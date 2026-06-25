<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

/**
 * Class UserRepository
 *
 * Handles database operations related to users.
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */    
    public function create(array $data): User
    {
        return User::create($data);
    }
    
    /**
     * Update a user's information.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    /**
     * Find a user by email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Revoke Current Token
     *
     * @param $user
     * @return bool
     */  
    public function revokeCurrentToken($user): bool {
        // For Laravel Sanctum
        return $user->currentAccessToken()->delete();
    }   
}
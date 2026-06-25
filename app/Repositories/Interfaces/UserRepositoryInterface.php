<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

/**
 * Interface UserRepositoryInterface
 *
 * Contract for user persistence operations.
 */
interface UserRepositoryInterface
{   
    /**
     * Create a new user record.
     *
     * @param array $data
     * @return User
     */    
    public function create(array $data): User;

    /**
     * Update a user's information.
     *
     * @param User $user
     * @param array $data
     * @return User
     */  
    public function update(User $user, array $data): User;

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */    
    public function findByEmail(string $email): ?User;

    /**
     * Revoke Current Token
     *
     * @param $user
     * @return bool
     */    
    public function revokeCurrentToken($user): bool;
}
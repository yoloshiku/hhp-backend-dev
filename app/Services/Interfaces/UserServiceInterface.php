<?php

namespace App\Services\Interfaces;

use App\Models\User;

/**
 * Interface UserServiceInterface
 *
 * Defines the contract for business logic related to user operations.
 *
 * The service layer acts as an intermediary between controllers
 * and repositories, encapsulating application business rules
 * related to user management.
 */
interface UserServiceInterface
{
    /**
     * Update the profile information of a given user.
     *
     * This method receives validated data from the controller
     * and applies the necessary business logic before delegating
     * the persistence to the repository layer.
     *
     * Example input:
     * [
     *     'name' => 'Jane Doe',
     *     'email' => 'jane@example.com'
     * ]
     *
     * @param User $user The user instance to be updated
     * @param array $data Validated profile data
     * @return User The updated User model instance
     */    
    public function updateProfile(User $user, array $data): User;
}
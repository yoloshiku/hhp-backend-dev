<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserService
 *
 * Handles business logic related to user operations.
 *
 * This service layer sits between the controllers and repositories,
 * ensuring that application logic is separated from database logic.
 */
class UserService implements UserServiceInterface
{
    /**
     * User repository instance.
     *
     * Responsible for database interactions related to the User model.
     *
     * @var UserRepositoryInterface
     */    
    protected $userRepository;

    /**
     * UserService constructor.
     *
     * Injects the UserRepositoryInterface implementation
     * through Laravel's dependency injection container.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    /**
     * Update a user's profile information.
     *
     * Receives validated profile data and delegates the
     * persistence logic to the repository layer.
     *
     * Example input:
     * [
     *     'name' => 'Jane Doe',
     *     'email' => 'jane@example.com'
     * ]
     *
     * @param User $user The user instance to update
     * @param array $data Validated profile data
     * @return User The updated User model instance
     */
    public function updateProfile(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        return $this->userRepository->update($user, $data);
    }
}
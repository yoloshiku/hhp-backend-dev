<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Services\Interfaces\UserServiceInterface;
use App\Http\Resources\UserResource;

/**
 * Class ProfileController
 *
 * Handles operations related to the authenticated user's profile.
 *
 * Responsibilities:
 * - Update the authenticated user's profile information.
 *
 * This controller delegates business logic to the UserService layer.
 */
class ProfileController extends Controller
{
    /**
     * User service instance.
     *
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * ProfileController constructor.
     *
     * Injects the UserServiceInterface implementation
     * through Laravel's service container.
     *
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Update the authenticated user's profile.
     *
     * Endpoint example:
     * PUT /api/user/profile
     *
     * The request is validated using UpdateProfileRequest.
     * Only validated fields will be passed to the service layer.
     *
     * Example payload:
     * {
     *   "name": "John Doe",
     *   "email": "john@example.com"
     * }
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request)
    {
        // Retrieve the currently authenticated user
        $user = $request->user();

        // Call the service layer to update the user's profile
        $updatedUser = $this->userService->updateProfile(
            $user,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'user' => new UserResource($updatedUser),
            ]
        ]);
    }
}
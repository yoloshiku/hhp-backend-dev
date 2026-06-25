<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Interfaces\AuthServiceInterface;
use App\Http\Resources\UserResource;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

/**
 * Class AuthController
 *
 * Handles authentication endpoints.
 */
class AuthController extends Controller
{
    /**
     * @var AuthServiceInterface
     */    
    protected $authService;

    /**
     * AuthController constructor.
     *
     * @param AuthServiceInterface $authService
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user.
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request)
    {
        $data = $this->authService->register(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'data' => [
                'user' => new UserResource($data['user']),
                'token' => $data['token']
            ]
        ])->setStatusCode(Response::HTTP_CREATED);
    }
    
    /**
     * Authenticate a user.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $data = $this->authService->login(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => new UserResource($data['user']),
                'token' => $data['token']
            ]
        ]);
    }

    /**
     * Logout a user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request) {
        $this->authService->logoutUser($request->user());
        
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }    
}
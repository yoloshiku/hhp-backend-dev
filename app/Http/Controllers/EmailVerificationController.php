<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Services\Interfaces\AuthServiceInterface;

/**
 * Class EmailVerificationController
 *
 * Handles email verification for API users.
 */
class EmailVerificationController extends Controller
{
    /**
     * Auth service instance.
     *
     * @var AuthServiceInterface
     */
    protected AuthServiceInterface $authService;

    /**
     * Constructor.
     *
     * @param AuthServiceInterface $authService
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Verify the user's email address.
     *
     * @param Request $request
     * @param int $id
     * @param string $hash
     * @return JsonResponse
     */
    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        // Validate the email hash
        if (!$this->isValidHash($user, $hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification link.'
            ], 403);
        }

        // Mark email as verified
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.'
        ]);
    }

    /**
     * Validate the verification hash.
     *
     * @param User $user
     * @param string $hash
     * @return bool
     */
    protected function isValidHash(User $user, string $hash): bool
    {
        return hash_equals(
            (string) $hash,
            sha1($user->getEmailForVerification())
        );
    }

    /**
     * Resend email verification notification.
     *
     * Endpoint:
     * POST /api/email/verification-notification
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resend(Request $request): JsonResponse
    {
        $user = $request->user();

        $this->authService->resendVerificationEmail($user);

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent.'
        ]);
    }    
}
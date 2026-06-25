<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\NewsletterSubscriberController;

/**
 * --------------------------------------------------------------------------
 * Email Verification Routes
 * --------------------------------------------------------------------------
 *
 * These routes handle email verification flow:
 *
 * - Verify email via signed URL (no authentication required)
 * - Resend verification email (requires authentication)
 *
 * Endpoints:
 * GET    /api/email/verify/{id}/{hash}
 * POST   /api/email/verification-notification
 * 
 * throttle restricts the number of times a user or IP address can 
 * access a specific route or functionality within a given time period
 */
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:3,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
    ->middleware(['auth:sanctum', 'throttle:3,1']);

/**
 * --------------------------------------------------------------------------
 * Authentication Routes
 * --------------------------------------------------------------------------
 *
 * Handles user authentication:
 *
 * - Register a new user
 * - Login an existing user
 *
 * Endpoints:
 * POST   /api/auth/register
 * POST   /api/auth/login
 * 
 * throttle restricts the number of times a user or IP address can 
 * access a specific route or functionality within a given time period
 */
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware(['throttle:2,1']);
    Route::post('login', [AuthController::class, 'login'])->middleware(['throttle:5,1']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware(['throttle:5,1', 'auth:sanctum']);
});

/**
 * --------------------------------------------------------------------------
 * Contact Routes
 * --------------------------------------------------------------------------
 *
 * Handles contact form submissions.
 *
 * Endpoint:
 * POST   /api/contact
 * 
 * throttle restricts the number of times a user or IP address can 
 * access a specific route or functionality within a given time period
 */
Route::post('contact', [ContactController::class, 'store'])->middleware(['throttle:5,1']);

/**
 * --------------------------------------------------------------------------
 * Protected User Routes
 * --------------------------------------------------------------------------
 *
 * These routes require:
 * - Authentication via Sanctum
 * - Verified email
 *
 * Middleware:
 * - auth:sanctum
 * - verified
 *
 * Endpoints:
 * PUT    /api/user/profile
 * 
 * throttle restricts the number of times a user or IP address can 
 * access a specific route or functionality within a given time period
 */
Route::middleware(['auth:sanctum', 'verified', 'throttle:5,1'])->group(function () {

    Route::prefix('user')->group(function () {
        Route::put('profile', [ProfileController::class, 'update']);
    });

});

/**
 * --------------------------------------------------------------------------
 * NewsletterSubscriber Routes
 * --------------------------------------------------------------------------
 *
 * Handles NewsletterSubscriber form submissions.
 *
 * Endpoint:
 * POST   /api/newsletter-subscriber
 * 
 * throttle restricts the number of times a user or IP address can 
 * access a specific route or functionality within a given time period
 */
Route::post('newsletter-subscriber', [NewsletterSubscriberController::class, 'store'])->middleware(['throttle:5,1']);

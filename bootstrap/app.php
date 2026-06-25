<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /**
         * 401 - Unauthenticated
         */
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            
            Log::warning('Unauthenticated request', [
                'message' => $e->getMessage(),
            ]);         

            return response()->json([
                'success' => false,
                'message' => 'You must be logged in.'
            ], 401);
        });

        /**
         * 403 - Unauthorized / Email not verified
         */
        $exceptions->render(function (AuthorizationException $e, Request $request) {

            Log::warning('Unauthorized action', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'This action is unauthorized.',
            ], 403);
        });

        /**
         * 422 - Validation errors
         */
        $exceptions->render(function (ValidationException $e, Request $request) {

            Log::info('Validation failed', [
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 422);
        });

        /**
         * 4xx / 5xx generic HTTP errors
         */
        $exceptions->render(function (HttpException $e, Request $request) {

            Log::error('HTTP exception', [
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);            
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'An error occurred.'
            ], $e->getStatusCode());
        });

        /**
         * Fallback - unexpected errors
         */
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {

                Log::error('Global exception caught', [
                    'type' => get_class($e),
                    'message' => $e->getMessage(),
                ]);               

                return response()->json([
                    'success' => false,
                    'message' => 'Server error.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
        });
    })->create();

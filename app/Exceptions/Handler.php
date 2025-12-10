<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Custom API error responses
        $this->renderable(function (Throwable $e, $request) {
            // Only apply to API requests
            if ($request->is('api/*')) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    /**
     * Handle API exceptions with REST standards
     */
    protected function handleApiException(Throwable $e, $request)
    {
        // Authorization/Forbidden errors (403)
        if ($e instanceof AccessDeniedHttpException) {
            return response()->json([
                'error' => [
                    'message' => $this->getAuthorizationMessage($request),
                    'type' => 'authorization_error',
                    'code' => 'FORBIDDEN',
                    'status' => 403
                ]
            ], 403);
        }

        if ($e instanceof AuthorizationException) {
            return response()->json([
                'error' => [
                    'message' => $this->getAuthorizationMessage($request),
                    'type' => 'authorization_error',
                    'code' => 'FORBIDDEN',
                    'status' => 403
                ]
            ], 403);
        }

        // Model Not Found (typically triggers 404)
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'error' => [
                    'message' => 'The requested resource was not found.',
                    'type' => 'not_found_error',
                    'code' => 'RESOURCE_NOT_FOUND',
                    'status' => 404
                ]
            ], 404);
        }

        // Not Found errors (404)
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'error' => [
                    'message' => 'The requested endpoint was not found.',
                    'type' => 'not_found_error',
                    'code' => 'ENDPOINT_NOT_FOUND',
                    'status' => 404
                ]
            ], 404);
        }

        // Method Not Allowed errors (405)
        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'error' => [
                    'message' => 'The HTTP method is not allowed for this endpoint.',
                    'type' => 'method_not_allowed_error',
                    'code' => 'METHOD_NOT_ALLOWED',
                    'status' => 405
                ]
            ], 405);
        }

        // Authentication errors (401)
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'error' => [
                    'message' => 'Authentication required. Please provide a valid token.',
                    'type' => 'authentication_error',
                    'code' => 'UNAUTHENTICATED',
                    'status' => 401
                ]
            ], 401);
        }

        // Validation errors (422)
        if ($e instanceof ValidationException) {
            return response()->json([
                'error' => [
                    'message' => 'The given data was invalid.',
                    'type' => 'validation_error',
                    'code' => 'VALIDATION_FAILED',
                    'status' => 422,
                    'details' => $e->errors()
                ]
            ], 422);
        }

        // Server errors (500)
        if ($this->isHttpException($e)) {
            return response()->json([
                'error' => [
                    'message' => 'An error occurred while processing your request.',
                    'type' => 'server_error',
                    'code' => 'SERVER_ERROR',
                    'status' => $e->getStatusCode()
                ]
            ], $e->getStatusCode());
        }

        // Default for other exceptions (500)
        return response()->json([
            'error' => [
                'message' => config('app.debug') 
                    ? $e->getMessage() 
                    : 'An unexpected error occurred.',
                'type' => 'internal_error',
                'code' => 'INTERNAL_ERROR',
                'status' => 500
            ]
        ], 500);
    }

    /**
     * Get context-specific authorization message
     */
    protected function getAuthorizationMessage($request)
    {
        $path = $request->path();

        // Leave application specific messages
        if (str_contains($path, 'api/leaves')) {
            if ($request->isMethod('GET') && preg_match('/api\/leaves\/\d+/', $path)) {
                return 'You do not have permission to view this leave application. You can only access your own leave records.';
            }
            if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
                return 'You can only edit your own pending leave applications.';
            }
            if ($request->isMethod('DELETE')) {
                return 'You can only delete your own pending leave applications.';
            }
            if (str_contains($path, 'cancel')) {
                return 'You can only cancel your own approved leave applications that have not started yet.';
            }
        }

        // Employee specific messages
        if (str_contains($path, 'api/employees')) {
            return 'You do not have permission to access this employee record.';
        }

        // Attendance specific messages
        if (str_contains($path, 'api/attendances')) {
            return 'You do not have permission to access this attendance record.';
        }

        // Default message
        return 'You do not have permission to perform this action.';
    }
}

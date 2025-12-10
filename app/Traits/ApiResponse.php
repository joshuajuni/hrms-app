<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Success response
     */
    protected function successResponse($data, string $message = null, int $status = 200): JsonResponse
    {
        $response = ['data' => $data];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $status);
    }

    /**
     * Success response with meta
     */
    protected function successResponseWithMeta($data, array $meta, string $message = null, int $status = 200): JsonResponse
    {
        $response = [
            'data' => $data,
            'meta' => $meta
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $status);
    }

    /**
     * Created response (201)
     */
    protected function createdResponse($data, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * No content response (204)
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message, string $type, string $code, int $status): JsonResponse
    {
        return response()->json([
            'error' => [
                'message' => $message,
                'type' => $type,
                'code' => $code,
                'status' => $status
            ]
        ], $status);
    }

    /**
     * Validation error response (422)
     */
    protected function validationErrorResponse(array $errors, string $message = 'The given data was invalid'): JsonResponse
    {
        return response()->json([
            'error' => [
                'message' => $message,
                'type' => 'validation_error',
                'code' => 'VALIDATION_FAILED',
                'status' => 422,
                'details' => $errors
            ]
        ], 422);
    }

    /**
     * Not found response (404)
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 'not_found_error', 'NOT_FOUND', 404);
    }

    /**
     * Unauthorized response (401)
     */
    protected function unauthorizedResponse(string $message = 'Authentication required'): JsonResponse
    {
        return $this->errorResponse($message, 'authentication_error', 'UNAUTHENTICATED', 401);
    }

    /**
     * Forbidden response (403)
     */
    protected function forbiddenResponse(string $message = 'You do not have permission to perform this action'): JsonResponse
    {
        return $this->errorResponse($message, 'authorization_error', 'FORBIDDEN', 403);
    }

    /**
     * Bad request response (400)
     */
    protected function badRequestResponse(string $message = 'Bad request'): JsonResponse
    {
        return $this->errorResponse($message, 'bad_request_error', 'BAD_REQUEST', 400);
    }

    /**
     * Server error response (500)
     */
    protected function serverErrorResponse(string $message = 'Internal server error'): JsonResponse
    {
        return $this->errorResponse($message, 'server_error', 'INTERNAL_ERROR', 500);
    }
}

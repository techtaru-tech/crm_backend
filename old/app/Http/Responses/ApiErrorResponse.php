<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

final class ApiErrorResponse
{
    /**
     * Return a standardized API error JSON response.
     *
     * Format: { "status": "error", "message": "...", "code": "..." }
     */
    public static function make(
        string $message,
        int $httpStatus = 400,
        string $code = '',
        array $extra = [],
    ): JsonResponse {
        $body = [
            'status'  => 'error',
            'message' => $message,
        ];

        if ($code !== '') {
            $body['code'] = $code;
        }

        if (! empty($extra)) {
            $body = array_merge($body, $extra);
        }

        return response()->json($body, $httpStatus);
    }

    public static function unauthorized(string $message = 'Unauthorized — invalid or missing API key.'): JsonResponse
    {
        return static::make($message, 401, 'UNAUTHORIZED');
    }

    public static function forbidden(string $message = 'Forbidden — API key lacks the required scope.'): JsonResponse
    {
        return static::make($message, 403, 'FORBIDDEN');
    }

    public static function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return static::make($message, 404, 'NOT_FOUND');
    }

    public static function rateLimited(string $message = 'Too many requests — rate limit exceeded.'): JsonResponse
    {
        return static::make($message, 429, 'RATE_LIMITED');
    }

    public static function validation(array $errors, string $message = 'The given data was invalid.'): JsonResponse
    {
        return static::make($message, 422, 'VALIDATION_FAILED', ['errors' => $errors]);
    }

    public static function serverError(string $message = 'An unexpected error occurred.'): JsonResponse
    {
        return static::make($message, 500, 'SERVER_ERROR');
    }
}

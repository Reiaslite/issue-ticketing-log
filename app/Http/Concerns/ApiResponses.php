<?php

namespace App\Http\Concerns;

use Illuminate\Http\JsonResponse;

/**
 * Provides the standard JSON envelope used by every API response.
 *
 * Success responses contain `success`, `message`, and `data`.
 * Error responses contain `success`, `message`, and `errors`.
 */
trait ApiResponses
{
    /**
     * Build a successful API response using the contract response shape.
     *
     * @param  string  $message  Human-readable success message.
     * @param  mixed  $data  Response payload, resource, collection, or null.
     * @param  int  $status  HTTP status code.
     * @param  array<string, mixed>  $extra  Additional top-level keys such as pagination meta.
     */
    protected function successResponse(string $message, mixed $data = null, int $status = 200, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $extra), $status);
    }

    /**
     * Build an error API response using the contract response shape.
     *
     * @param  string  $message  Human-readable error message.
     * @param  mixed  $errors  Validation errors, structured details, or null.
     * @param  int  $status  HTTP status code.
     */
    protected function errorResponse(string $message, mixed $errors = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}

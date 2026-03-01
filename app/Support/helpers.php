<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('json_422')) {
    function json_422(string $message, array $extra = []): JsonResponse
    {
        return response()->json(
            array_merge(['error' => $message], $extra), 422
        );
    }
}

if (!function_exists('json_500')) {
    function json_500(string $message, array $extra = []): JsonResponse
    {
        return response()->json(
            array_merge(['error' => $message], $extra), 500
        );
    }
}
<?php

namespace App\Helpers;

class ApiResponseFormatter
{
    public static function ok(array $data = [])
    {
        return response()->json([
            'data' => $data
        ]);
    }

    public static function notfound(string $message = 'Not Found')
    {
        return response()->json([
            'message' => $message
        ], 404);
    }

    public static function unauthorized(string $message = 'Unauthorized')
    {
        return response()->json([
            'message' => $message
        ], 401);
    }

    public static function forbidden(string $message = 'Forbidden')
    {
        return response()->json([
            'message' => $message
        ], 403);
    }

    public static function unprocessible(string $message = 'Unprocessible')
    {
        return response()->json([
            'message' => $message
        ], 422);
    }

    public static function internalServerError(string $message = 'Internal Server Error')
    {
        return response()->json([
            'message' => $message
        ], 500);
    }
}

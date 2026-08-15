<?php

namespace App\Http\Controllers\Api;

abstract class Controller
{
    public function formatResponse(string $message, $data = [], $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public function formatError(string $message, int $status = 500, $errors = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    public function formatValidationError(string $message, array $errors, $status = 422)
    {
        return response()->json([
            'success' => false,
            'messa  ge' => $message,
            'errors' => $errors,
        ], $status);
    }
}

<?php

namespace App\Http\Controllers\Profile;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileShowController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('role');

        return ApiResponseFormatter::ok([
            'name' => $user->name,
            'email' => $user->email,
            'role' => [
                'code' => $user->role?->code,
                'name' => $user->role?->name,
            ],
        ]);
    }
}

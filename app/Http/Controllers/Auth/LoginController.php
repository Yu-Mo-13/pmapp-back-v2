<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Request\LoginRequest;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');
        info("Logging in user: $email $password");
        return ApiResponseFormatter::ok();
    }
}

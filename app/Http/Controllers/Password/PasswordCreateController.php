<?php

namespace App\Http\Controllers\Password;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Password\PasswordCreateRequest;
use App\Models\Password;
use Illuminate\Http\JsonResponse;

class PasswordCreateController extends Controller
{
    public function __invoke(PasswordCreateRequest $request): JsonResponse
    {
        $passwordData = $request->input('password');

        Password::create([
            'password' => $passwordData['password'],
            'application_id' => $passwordData['application_id'],
            'account_id' => $passwordData['account_id'],
        ]);

        return ApiResponseFormatter::ok();
    }
}

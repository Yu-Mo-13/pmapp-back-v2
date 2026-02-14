<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            return ApiResponseFormatter::ok([
                'name' => $user->name,
            ]);
        } catch (\Exception $e) {
            info('Error fetching login status: ' . $e->getMessage());
            return ApiResponseFormatter::ok([
                'name' => 'ゲスト',
            ]);
        }

    }
}

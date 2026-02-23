<?php

namespace App\Http\Controllers\UnregistedPassword;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\UnregistedPassword;
use Illuminate\Http\JsonResponse;

class UnregistedPasswordDeleteAllController extends Controller
{
    public function __invoke(): JsonResponse
    {
        UnregistedPassword::query()->delete();
        return ApiResponseFormatter::ok();
    }
}

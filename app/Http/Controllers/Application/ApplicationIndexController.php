<?php

namespace App\Http\Controllers\Application;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;

class ApplicationIndexController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return ApiResponseFormatter::ok();
    }
}

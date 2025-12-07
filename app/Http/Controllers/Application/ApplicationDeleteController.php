<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Helpers\ApiResponseFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApplicationDeleteController extends Controller
{
    public function __invoke(Application $application): JsonResponse
    {
        $application->delete();
        return ApiResponseFormatter::ok();
    }
}

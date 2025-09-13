<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Helpers\ApiResponseFormatter;
use Illuminate\Http\JsonResponse;

class ApplicationShowController extends Controller
{
    public function __invoke(Application $application): JsonResponse
    {
        $applicationWithoutTimestamps = $application->makeHidden(['created_at', 'updated_at']);
        return ApiResponseFormatter::ok($applicationWithoutTimestamps->toArray());
    }
}

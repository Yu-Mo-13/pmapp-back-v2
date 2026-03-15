<?php

namespace App\Http\Controllers\Healthcheck;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Healthcheck;
use Illuminate\Http\JsonResponse;

class HealthcheckCreateController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $healthcheck = Healthcheck::query()->create();

        return ApiResponseFormatter::ok($healthcheck->toArray());
    }
}

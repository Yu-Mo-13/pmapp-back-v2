<?php

namespace App\Http\Controllers\Healthcheck;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Healthcheck;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class HealthcheckStatusShowController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            $healthcheck = Healthcheck::query()
                ->whereDate('created_at', Carbon::today())
                ->latest('created_at')
                ->first();

            if ($healthcheck === null) {
                return ApiResponseFormatter::ok([
                    'is_healthy' => false,
                    'message' => 'Healthcheck failed: no record found for today.',
                ]);
            }

            return ApiResponseFormatter::ok([
                'id' => $healthcheck->id,
                'is_healthy' => true,
                'message' => 'Healthcheck succeeded.',
            ]);
        } catch (\Exception $e) {
            info('Healthcheck status lookup failed: ' . $e->getMessage());

            return ApiResponseFormatter::ok([
                'is_healthy' => false,
                'message' => 'Healthcheck failed.',
            ]);
        }
    }
}

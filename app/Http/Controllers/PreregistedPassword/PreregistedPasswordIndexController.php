<?php

namespace App\Http\Controllers\PreregistedPassword;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\PreregistedPassword;
use Illuminate\Http\JsonResponse;

class PreregistedPasswordIndexController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $preregistedPasswords = PreregistedPassword::with(['application', 'account'])
            ->orderBy('created_at', 'desc')
            ->get();

        $response = $preregistedPasswords->map(function ($preregistedPassword) {
            return [
                'uuid' => $preregistedPassword->uuid,
                'application' => $preregistedPassword->application ? [
                    'id' => $preregistedPassword->application->id,
                    'name' => $preregistedPassword->application->name,
                ] : null,
                'account' => $preregistedPassword->account ? [
                    'id' => $preregistedPassword->account->id,
                    'name' => $preregistedPassword->account->name,
                ] : null,
                'created_at' => optional($preregistedPassword->created_at)->toISOString(),
            ];
        })->toArray();

        return ApiResponseFormatter::ok($response);
    }
}

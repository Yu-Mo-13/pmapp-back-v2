<?php

namespace App\Http\Controllers\UnregistedPassword;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\UnregistedPassword;
use Illuminate\Http\JsonResponse;

class UnregistedPasswordIndexController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $unregistedPasswords = UnregistedPassword::with(['application', 'account'])
            ->orderBy('created_at', 'desc')
            ->get();

        $response = $unregistedPasswords->map(function ($unregistedPassword) {
            return [
                'uuid' => $unregistedPassword->uuid,
                'application' => $unregistedPassword->application ? [
                    'id' => $unregistedPassword->application->id,
                    'name' => $unregistedPassword->application->name,
                ] : null,
                'account' => $unregistedPassword->account ? [
                    'id' => $unregistedPassword->account->id,
                    'name' => $unregistedPassword->account->name,
                ] : null,
                'created_at' => optional($unregistedPassword->created_at)->toISOString(),
            ];
        })->toArray();

        return ApiResponseFormatter::ok($response);
    }
}

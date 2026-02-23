<?php

namespace App\Http\Controllers\UnregistedPassword;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\UnregistedPassword;
use Illuminate\Http\JsonResponse;

class UnregistedPasswordShowController extends Controller
{
    public function __invoke(UnregistedPassword $unregistedPassword): JsonResponse
    {
        $unregistedPassword->load(['application', 'account']);

        return ApiResponseFormatter::ok([
            'uuid' => $unregistedPassword->uuid,
            'password' => $unregistedPassword->password,
            'application' => $unregistedPassword->application ? [
                'id' => $unregistedPassword->application->id,
                'name' => $unregistedPassword->application->name,
            ] : null,
            'account' => $unregistedPassword->account ? [
                'id' => $unregistedPassword->account->id,
                'name' => $unregistedPassword->account->name,
            ] : null,
            'created_at' => optional($unregistedPassword->created_at)->toISOString(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\PreregistedPassword;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\PreregistedPassword;
use Illuminate\Http\JsonResponse;

class PreregistedPasswordShowController extends Controller
{
    public function __invoke(PreregistedPassword $preregistedPassword): JsonResponse
    {
        $preregistedPassword->load(['application', 'account']);

        return ApiResponseFormatter::ok([
            'uuid' => $preregistedPassword->uuid,
            'password' => $preregistedPassword->password,
            'application' => $preregistedPassword->application ? [
                'id' => $preregistedPassword->application->id,
                'name' => $preregistedPassword->application->name,
            ] : null,
            'account' => $preregistedPassword->account ? [
                'id' => $preregistedPassword->account->id,
                'name' => $preregistedPassword->account->name,
            ] : null,
            'created_at' => optional($preregistedPassword->created_at)->toISOString(),
        ]);
    }
}

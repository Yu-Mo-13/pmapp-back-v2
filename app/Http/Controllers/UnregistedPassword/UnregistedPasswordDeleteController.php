<?php

namespace App\Http\Controllers\UnregistedPassword;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\UnregistedPassword;
use Illuminate\Http\JsonResponse;

class UnregistedPasswordDeleteController extends Controller
{
    public function __invoke(UnregistedPassword $unregistedPassword): JsonResponse
    {
        $unregistedPassword->delete();

        return ApiResponseFormatter::ok([
            'message' => 'Unregisted password deleted successfully.',
        ]);
    }
}

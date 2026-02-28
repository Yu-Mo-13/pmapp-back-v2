<?php

namespace App\Http\Controllers\PreregistedPassword;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\PreregistedPassword;
use Illuminate\Http\JsonResponse;

class PreregistedPasswordDeleteController extends Controller
{
    public function __invoke(PreregistedPassword $preregistedPassword): JsonResponse
    {
        $preregistedPassword->delete();

        return ApiResponseFormatter::ok([
            'message' => 'Preregisted password deleted successfully.',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Application;

use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Application\ApplicationCreateRequest;
use App\Models\Application;

class ApplicationCreateController extends Controller
{
    public function __invoke(ApplicationCreateRequest $request): JsonResponse
    {
        $application = $request->input('application');
        Application::create([
            'name' => $application['name'],
            'account_class' => $application['account_class'],
            'notice_class' => $application['notice_class'],
            'mark_class' => $application['mark_class'],
            'pre_password_size' => $application['pre_password_size']
        ]);
        return ApiResponseFormatter::ok();
    }
}

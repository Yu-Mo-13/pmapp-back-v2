<?php

namespace App\Http\Controllers\Application;

use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationCreateRequest;
use App\Models\Application;

class ApplicationCreateController extends Controller
{
    public function __invoke(ApplicationCreateRequest $request): JsonResponse
    {
        Application::create([
            'name' => $request->input('name'),
            'account_class' => $request->input('account_class'),
            'notice_class' => $request->input('notice_class'),
            'mark_class' => $request->input('mark_class'),
            'pre_password_size' => $request->input('pre_password_size')
        ]);
        return ApiResponseFormatter::ok();
    }
}

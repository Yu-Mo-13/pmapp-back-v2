<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseFormatter;
use App\Http\Requests\Application\ApplicationUpdateRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Application;

class ApplicationUpdateController extends Controller
{
    public function __invoke(ApplicationUpdateRequest $request, Application $application): JsonResponse
    {
        $validated = $request->validated();
        $applicationData = $validated['application'];

        $application->update([
            'name' => $applicationData['name'],
            'account_class' => $applicationData['account_class'],
            'notice_class' => $applicationData['notice_class'],
            'mark_class' => $applicationData['mark_class'],
            'pre_password_size' => $applicationData['pre_password_size']
        ]);

        return ApiResponseFormatter::ok();
    }
}

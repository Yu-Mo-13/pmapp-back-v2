<?php

namespace App\Http\Controllers\Password;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Password\PasswordLatestShowRequest;
use App\Models\Application;
use App\Models\Password;
use Illuminate\Http\JsonResponse;

class PasswordLatestShowController extends Controller
{
    public function __invoke(PasswordLatestShowRequest $request): JsonResponse
    {
        $applicationId = $request->input('application_id');
        $accountId = $request->input('account_id');
        $application = Application::query()->find($applicationId);

        if ($application && !$application->account_class && !is_null($accountId)) {
            return ApiResponseFormatter::notfound();
        }
        if ($application && $application->account_class && is_null($accountId)) {
            return ApiResponseFormatter::notfound();
        }

        $latestPasswordQuery = Password::query()
            ->where('application_id', $applicationId);

        if ($application && $application->account_class) {
            $latestPasswordQuery->where('account_id', $accountId);
        }

        $latestPassword = $latestPasswordQuery
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        if (!$latestPassword) {
            return ApiResponseFormatter::notfound();
        }

        return ApiResponseFormatter::ok([
            'password' => $latestPassword->password,
        ]);
    }
}

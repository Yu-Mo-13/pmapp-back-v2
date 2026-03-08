<?php

namespace App\Http\Controllers\Password;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Password\PasswordLatestShowRequest;
use App\Models\Application;
use App\Models\Password;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PasswordLatestShowController extends Controller
{
    public function __invoke(PasswordLatestShowRequest $request): JsonResponse
    {
        $applicationId = $request->input('application_id');
        $accountId = $request->input('account_id');
        $application = Application::query()->find($applicationId);

        if ($application && !$application->account_class && !is_null($accountId)) {
            Log::info('パスワード最新取得: account_class=false のアプリケーションで account_id が指定されたため404を返却します。', [
                'application_id' => $applicationId,
                'account_id' => $accountId,
            ]);
            return ApiResponseFormatter::notfound();
        }
        if ($application && $application->account_class && is_null($accountId)) {
            Log::info('パスワード最新取得: account_class=true のアプリケーションで account_id が未指定のため404を返却します。', [
                'application_id' => $applicationId,
            ]);
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
            Log::info('パスワード最新取得: 対象のパスワードレコードが存在しないため404を返却します。', [
                'application_id' => $applicationId,
                'account_id' => $accountId,
            ]);
            return ApiResponseFormatter::notfound();
        }

        return ApiResponseFormatter::ok([
            'password' => $latestPassword->password,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseFormatter;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Account\AccountCreateRequest;
use App\Models\Account;
use App\Models\Application;

class AccountCreateController extends Controller
{
    public function __invoke(AccountCreateRequest $request): JsonResponse
    {
        $account = $request->input('account');
        info($account);

        if (!$this->validateApplication($account)) {
            return ApiResponseFormatter::forbidden('アカウントの作成に失敗しました。');
        }

        Account::create([
            'name' => $account['name'],
            'application_id' => $account['application_id'],
            'notice_class' => $account['notice_class'],
        ]);

        return ApiResponseFormatter::ok();
    }

    private function validateApplication(mixed $account): bool
    {
        $application = Application::where('id', $account['application_id'])->first();
        info($application);

        if (!$application || !$application->account_class) {
            return false;
        }

        return true;
    }
}

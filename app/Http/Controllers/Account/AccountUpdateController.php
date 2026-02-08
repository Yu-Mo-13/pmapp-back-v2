<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\AccountUpdateRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Account;
use App\Helpers\ApiResponseFormatter;

class AccountUpdateController extends Controller
{
    public function __invoke(AccountUpdateRequest $request, Account $account): JsonResponse
    {
        if (!$account->application || !$account->application->account_class) {
            return ApiResponseFormatter::notfound();
        }

        $validated = $request->validated();
        $accountData = $validated['account'];

        $account->update([
            'name' => $accountData['name'],
            'notice_class' => $accountData['notice_class'],
        ]);

        return ApiResponseFormatter::ok();
    }
}

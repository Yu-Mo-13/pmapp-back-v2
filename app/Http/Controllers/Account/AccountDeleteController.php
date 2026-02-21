<?php

namespace App\Http\Controllers\Account;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;

class AccountDeleteController extends Controller
{
    public function __invoke(Account $account): JsonResponse
    {
        if (!$account->application || !$account->application->account_class) {
            return ApiResponseFormatter::notfound();
        }

        $account->delete();
        return ApiResponseFormatter::ok();
    }
}

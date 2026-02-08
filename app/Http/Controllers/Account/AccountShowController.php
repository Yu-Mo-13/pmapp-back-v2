<?php

namespace App\Http\Controllers\Account;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;

class AccountShowController extends Controller
{
    public function __invoke(Account $account): JsonResponse
    {
        $account = $account->makeHidden(['created_at', 'updated_at']);
        return ApiResponseFormatter::ok($account->toArray());
    }
}

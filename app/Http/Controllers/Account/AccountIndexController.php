<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseFormatter;
use App\Models\Account;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class AccountIndexController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $accounts = Account::with('application')->orderBy('id')->get();
        $transformAccounts = $this->transformAccounts($accounts);
        return ApiResponseFormatter::ok($transformAccounts);
    }

    private function transformAccounts(Collection $accounts): array
    {
        return $accounts->map(fn($account) => [
            'id' => $account->id,
            'name' => $account->name,
            'application_id' => $account->application_id,
            'application_name' => $account->application->name,
            'notice_class' => $account->notice_class,
        ])->toArray();
    }
}

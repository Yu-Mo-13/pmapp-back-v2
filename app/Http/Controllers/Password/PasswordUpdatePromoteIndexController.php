<?php

namespace App\Http\Controllers\Password;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Application;
use App\Models\Password;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PasswordUpdatePromoteIndexController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $threshold = Carbon::now()->subMonths(config('app.password_update_promote_months'));

        $applications = $this->findApplicationTargets($threshold);
        $accounts = $this->findAccountTargets($threshold);

        $response = $applications
            ->concat($accounts)
            ->sortBy([
                ['application.id', 'asc'],
                ['account.id', 'asc'],
            ])
            ->values()
            ->all();

        return ApiResponseFormatter::ok($response);
    }

    private function findApplicationTargets(Carbon $threshold): Collection
    {
        $latestPasswords = Password::query()
            ->selectRaw('application_id, MAX(created_at) as latest_created_at')
            ->whereNull('account_id')
            ->groupBy('application_id')
            ->toBase();

        return Application::query()
            ->joinSub($latestPasswords, 'latest_passwords', function ($join) {
                $join->on('latest_passwords.application_id', '=', 'applications.id');
            })
            ->where('applications.account_class', false)
            ->where('latest_passwords.latest_created_at', '<=', $threshold)
            ->orderBy('applications.id')
            ->get([
                'applications.id',
                'applications.name',
            ])
            ->map(function (Application $application): array {
                return [
                    'application' => [
                        'id' => $application->id,
                        'name' => $application->name,
                    ],
                    'account' => null,
                ];
            });
    }

    private function findAccountTargets(Carbon $threshold): Collection
    {
        $latestPasswords = Password::query()
            ->selectRaw('application_id, account_id, MAX(created_at) as latest_created_at')
            ->whereNotNull('account_id')
            ->groupBy('application_id', 'account_id')
            ->toBase();

        return Account::query()
            ->join('applications', 'applications.id', '=', 'accounts.application_id')
            ->joinSub($latestPasswords, 'latest_passwords', function ($join) {
                $join->on('latest_passwords.application_id', '=', 'accounts.application_id')
                    ->on('latest_passwords.account_id', '=', 'accounts.id');
            })
            ->where('applications.account_class', true)
            ->where('latest_passwords.latest_created_at', '<=', $threshold)
            ->orderBy('applications.id')
            ->orderBy('accounts.id')
            ->get([
                'accounts.id',
                'accounts.name',
                'accounts.application_id',
                DB::raw('applications.id as application_row_id'),
                DB::raw('applications.name as application_name'),
            ])
            ->map(function (Account $account): array {
                return [
                    'application' => [
                        'id' => $account->application_row_id,
                        'name' => $account->application_name,
                    ],
                    'account' => [
                        'id' => $account->id,
                        'name' => $account->name,
                    ],
                ];
            });
    }
}

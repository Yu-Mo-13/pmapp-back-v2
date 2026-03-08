<?php

namespace App\Http\Controllers\Application;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class ApplicationAccountIndexController extends Controller
{
    public function __invoke(Application $application): JsonResponse
    {
        $accounts = $application->accounts()
            ->orderBy('id')
            ->get();

        return ApiResponseFormatter::ok($this->transformAccounts($accounts));
    }

    private function transformAccounts(Collection $accounts): array
    {
        return $accounts->map(fn($account) => [
            'id' => $account->id,
            'name' => $account->name,
        ])->toArray();
    }
}

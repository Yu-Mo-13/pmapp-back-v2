<?php

namespace App\Http\Controllers\Account;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Collection;

class AccountApplicationIndexController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $applications = Application::where('account_class', true)
            ->orderBy('id')
            ->get();

        $transformedApplications = $this->transformApplications($applications);

        return ApiResponseFormatter::ok($transformedApplications);
    }

    private function transformApplications(Collection $applications): array
    {
        return $applications->map(fn($application) => [
            'id' => $application->id,
            'name' => $application->name,
        ])->toArray();
    }
}

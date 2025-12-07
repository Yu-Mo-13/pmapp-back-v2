<?php

namespace App\Http\Controllers\Application;

use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Database\Eloquent\Collection;

class ApplicationIndexController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $applications = Application::orderBy('id')->get();
        $transformApplications = $this->transformApplications($applications);
        return ApiResponseFormatter::ok($transformApplications);
    }

    private function transformApplications(Collection $applications): array
    {
        return $applications->map(fn($application) => [
            'id' => $application->id,
            'name' => $application->name,
            'account_class' => $application->account_class,
            'notice_class' => $application->notice_class,
            'mark_class' => $application->mark_class
        ])->toArray();
    }
}

<?php

namespace App\Http\Controllers\Password;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Password\PasswordIndexRequest;
use App\Models\Application;
use Illuminate\Http\JsonResponse;

class PasswordIndexController extends Controller
{
    public function __invoke(PasswordIndexRequest $request): JsonResponse
    {
        $applicationId = $request->input('application_id');

        $applications = Application::query()
            ->with(['accounts'])
            ->when($applicationId, function ($query, $applicationId) {
                $query->where('id', $applicationId);
            })
            ->orderBy('id')
            ->get();

        $response = [];

        foreach ($applications as $application) {
            if ($application->account_class) {
                foreach ($application->accounts->sortBy('id') as $account) {
                    $response[] = [
                        'application' => [
                            'id' => $application->id,
                            'name' => $application->name,
                        ],
                        'account' => [
                            'id' => $account->id,
                            'name' => $account->name,
                        ],
                    ];
                }
                continue;
            }

            $response[] = [
                'application' => [
                    'id' => $application->id,
                    'name' => $application->name,
                ],
                'account' => null,
            ];
        }

        return ApiResponseFormatter::ok($response);
    }
}

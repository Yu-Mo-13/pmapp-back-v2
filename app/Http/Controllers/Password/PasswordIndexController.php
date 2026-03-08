<?php

namespace App\Http\Controllers\Password;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Password\PasswordIndexRequest;
use App\Services\Password\PasswordIndexService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class PasswordIndexController extends Controller
{
    private PasswordIndexService $passwordIndexService;

    public function __construct(PasswordIndexService $passwordIndexService)
    {
        $this->passwordIndexService = $passwordIndexService;
    }

    public function __invoke(PasswordIndexRequest $request): JsonResponse
    {
        $applicationId = $request->input('application_id');
        $indexData = $this->passwordIndexService->fetchIndexData($applicationId);
        $applications = $indexData['applications'];
        $latestUpdatedAtByApplicationAndAccount = $indexData['latest_updated_at_by_application_and_account'];
        $latestUpdatedAtByApplication = $indexData['latest_updated_at_by_application'];

        $response = [];

        foreach ($applications as $application) {
            if ($application->account_class) {
                foreach ($application->accounts->sortBy('id') as $account) {
                    $latestUpdatedAt = $latestUpdatedAtByApplicationAndAccount->get(
                        sprintf('%d:%d', $application->id, $account->id)
                    );
                    $response[] = [
                        'latest_updated_at' => $latestUpdatedAt ? Carbon::parse($latestUpdatedAt)->toISOString() : null,
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

            $latestUpdatedAt = $latestUpdatedAtByApplication->get($application->id);
            $response[] = [
                'latest_updated_at' => $latestUpdatedAt ? Carbon::parse($latestUpdatedAt)->toISOString() : null,
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

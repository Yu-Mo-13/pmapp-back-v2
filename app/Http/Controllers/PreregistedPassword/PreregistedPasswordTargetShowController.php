<?php

namespace App\Http\Controllers\PreregistedPassword;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreregistedPasswordTargetShowController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $applicationId = $request->query('application_id');

        if (filter_var($applicationId, FILTER_VALIDATE_INT) === false) {
            return ApiResponseFormatter::notfound();
        }

        $application = Application::query()->find($applicationId);

        if (! $application) {
            return ApiResponseFormatter::notfound();
        }

        $accountId = $request->query('account_id');

        if (! $application->account_class) {
            if (! is_null($accountId)) {
                return ApiResponseFormatter::notfound();
            }

            return $this->response($application, null);
        }

        if (filter_var($accountId, FILTER_VALIDATE_INT) === false) {
            return ApiResponseFormatter::notfound();
        }

        $account = Account::query()
            ->where('application_id', $application->id)
            ->find($accountId);

        if (! $account) {
            return ApiResponseFormatter::notfound();
        }

        return $this->response($application, $account);
    }

    private function response(Application $application, ?Account $account): JsonResponse
    {
        return ApiResponseFormatter::ok([
            'application' => [
                'id' => $application->id,
                'name' => $application->name,
            ],
            'account' => $account ? [
                'id' => $account->id,
                'name' => $account->name,
            ] : null,
        ]);
    }
}

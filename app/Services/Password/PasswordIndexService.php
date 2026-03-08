<?php

namespace App\Services\Password;

use App\Models\Application;
use App\Models\Password;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class PasswordIndexService
{
    /**
     * @return array{
     *     applications:EloquentCollection<int, Application>,
     *     latest_updated_at_by_application_and_account:Collection<string, mixed>,
     *     latest_updated_at_by_application:Collection<int, mixed>
     * }
     */
    public function fetchIndexData(?int $applicationId): array
    {
        $latestUpdatedAtByApplicationAndAccount = Password::query()
            ->selectRaw('application_id, account_id, MAX(updated_at) as latest_updated_at')
            ->when($applicationId, function ($query, $applicationId) {
                $query->where('application_id', $applicationId);
            })
            ->groupBy('application_id', 'account_id')
            ->get()
            ->mapWithKeys(function ($password) {
                return [sprintf('%d:%d', $password->application_id, $password->account_id) => $password->latest_updated_at];
            });

        $latestUpdatedAtByApplication = Password::query()
            ->selectRaw('application_id, MAX(updated_at) as latest_updated_at')
            ->when($applicationId, function ($query, $applicationId) {
                $query->where('application_id', $applicationId);
            })
            ->groupBy('application_id')
            ->get()
            ->mapWithKeys(function ($password) {
                return [$password->application_id => $password->latest_updated_at];
            });

        $applications = Application::query()
            ->with(['accounts'])
            ->when($applicationId, function ($query, $applicationId) {
                $query->where('id', $applicationId);
            })
            ->orderBy('id')
            ->get();

        return [
            'applications' => $applications,
            'latest_updated_at_by_application_and_account' => $latestUpdatedAtByApplicationAndAccount,
            'latest_updated_at_by_application' => $latestUpdatedAtByApplication,
        ];
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserIndexRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserIndexController extends Controller
{
    public function __invoke(UserIndexRequest $request): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ユーザー名', 'メールアドレス', 'ロール名', 'Supabase UID', '削除日時'], ',', '"', '');

            $users = User::query()->with('role')->lazyById();
            foreach ($users as $user) {
                $row = [
                    $user->name,
                    $this->mask($user->email),
                    $user->role->name ?? '',
                    $this->mask($user->uid ?? ''),
                    $user->deleted_at ?? '',
                ];
                fputcsv($output, $row, ',', '"', '');
            }

            fclose($output);
        }, 'users.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    private function mask(string $value): string
    {
        return mb_substr($value, 0, 5) . str_repeat('*', max(0, mb_strlen($value) - 5));
    }
}

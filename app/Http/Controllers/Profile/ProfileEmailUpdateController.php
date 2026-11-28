<?php

namespace App\Http\Controllers\Profile;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileEmailUpdateRequest;
use App\Services\SupabaseAuthService;
use Exception;
use Illuminate\Http\JsonResponse;

class ProfileEmailUpdateController extends Controller
{
    private SupabaseAuthService $supabaseAuth;

    public function __construct(SupabaseAuthService $supabaseAuth)
    {
        $this->supabaseAuth = $supabaseAuth;
    }

    public function __invoke(ProfileEmailUpdateRequest $request): JsonResponse
    {
        try {
            // Supabase側のメールアドレス変更を申請する。
            // 実際の変更は新しいメールアドレス宛の確認メールによる
            // 所有確認が完了した時点で確定するため、ローカルDBの
            // メールアドレスはここでは更新しない。
            // 確定後のメールアドレスは、次回以降のリクエスト時に
            // SupabaseGuardがSupabaseの情報と同期する。
            $this->supabaseAuth->updateUser($request->bearerToken(), [
                'email' => $request->input('profile.email'),
            ]);
        } catch (Exception $e) {
            info('Profile email update failed: ' . $e->getMessage());
            return ApiResponseFormatter::unprocessible('メールアドレスの変更に失敗しました。');
        }

        return ApiResponseFormatter::ok();
    }
}

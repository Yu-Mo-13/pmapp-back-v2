<?php

namespace App\Http\Controllers\Profile;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfilePasswordUpdateRequest;
use App\Services\SupabaseAuthService;
use Exception;
use Illuminate\Http\JsonResponse;

class ProfilePasswordUpdateController extends Controller
{
    private SupabaseAuthService $supabaseAuth;

    public function __construct(SupabaseAuthService $supabaseAuth)
    {
        $this->supabaseAuth = $supabaseAuth;
    }

    public function __invoke(ProfilePasswordUpdateRequest $request): JsonResponse
    {
        $user = $request->user();

        try {
            // 現在のパスワードで再認証する
            $this->supabaseAuth->signIn($user->email, $request->input('profile.current_password'));
        } catch (Exception $e) {
            return ApiResponseFormatter::unprocessible('現在のパスワードが正しくありません。');
        }

        try {
            $this->supabaseAuth->updateUser($request->bearerToken(), [
                'password' => $request->input('profile.password'),
            ]);
        } catch (Exception $e) {
            info('Profile password update failed: ' . $e->getMessage());
            return ApiResponseFormatter::unprocessible('パスワードの変更に失敗しました。');
        }

        return ApiResponseFormatter::ok();
    }
}

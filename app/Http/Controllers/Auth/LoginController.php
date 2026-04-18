<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\SupabaseAuthService;
use Exception;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    private SupabaseAuthService $supabaseAuth;

    public function __construct(SupabaseAuthService $supabaseAuth)
    {
        $this->supabaseAuth = $supabaseAuth;
    }

    public function __invoke(LoginRequest $request): JsonResponse
    {
        \Log::info('Login attempt started', ['email' => $request->input('email')]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::with('role')
            ->where('email', $email)
            ->whereNotNull('uid')
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            \Log::info('ユーザーが見つかりませんでした。');
            return ApiResponseFormatter::unprocessible('ログインに失敗しました。');
        }

        try {
            // Supabaseで認証
            $authResult = $this->supabaseAuth->signIn($email, $password);

            return ApiResponseFormatter::ok([
                'access_token' => $authResult['access_token'],
                'top_page_url' => $user->role->top_page_url,
            ]);
        } catch (Exception $e) {
            info("Login failed for user: $email - " . $e->getMessage());
            return ApiResponseFormatter::unprocessible('ログインに失敗しました。');
        }
    }
}

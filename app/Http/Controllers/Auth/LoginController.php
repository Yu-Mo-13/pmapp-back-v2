<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Request\LoginRequest;
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

        try {
            // Supabaseで認証
            $authResult = $this->supabaseAuth->signIn($email, $password);

            // ローカルユーザーとの同期
            $localUser = $this->syncUserWithDatabase($authResult['user']);

            if (!$localUser) {
                return ApiResponseFormatter::internalServerError('User synchronization failed');
            }

            \Log::info('Login successful', ['user_id' => $localUser->id]);

            return ApiResponseFormatter::ok([
                'access_token' => $authResult['access_token'],
            ]);

        } catch (Exception $e) {
            info("Login failed for user: $email - " . $e->getMessage());
            return ApiResponseFormatter::unauthorized('Invalid email or password');
        }
    }

    /**
     * Sync Supabase user with local database
     *
     * @param array $supabaseUser
     * @return User|null
     */
    private function syncUserWithDatabase(array $supabaseUser): ?User
    {
        $email = $supabaseUser['email'] ?? null;

        if (!$email) {
            return null;
        }

        $user = User::with('role')->where('email', $email)->first();

        if (!$user) {
            // デフォルトロールを取得（WEB_USERを仮定）
            $defaultRoleId = \App\Models\Role::where('code', 'WEB_USER')->first()?->id ?? 1;

            $user = User::create([
                'name' => $supabaseUser['user_metadata']['name'] ?? $supabaseUser['email'],
                'email' => $email,
                'role_id' => $defaultRoleId,
            ]);

            $user->load('role');
        }

        return $user;
    }
}

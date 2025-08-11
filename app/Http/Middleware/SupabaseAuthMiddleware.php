<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponseFormatter;
use App\Models\User;
use App\Services\SupabaseAuthService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupabaseAuthMiddleware
{
    private SupabaseAuthService $supabaseAuth;

    public function __construct(SupabaseAuthService $supabaseAuth)
    {
        $this->supabaseAuth = $supabaseAuth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return ApiResponseFormatter::unauthorized('Authorization header missing or invalid');
        }

        $token = substr($authHeader, 7);
        $supabaseUser = $this->supabaseAuth->verifyToken($token);

        if (!$supabaseUser) {
            return ApiResponseFormatter::unauthorized('Invalid or expired token');
        }

        // ローカルユーザーを取得または作成
        $localUser = $this->syncUserWithDatabase($supabaseUser);
        if (!$localUser) {
            return ApiResponseFormatter::internalServerError('User synchronization failed');
        }

        // リクエストにユーザー情報を追加
        $request->merge(['authenticated_user' => $localUser]);
        $request->setUserResolver(function () use ($localUser) {
            return $localUser;
        });

        return $next($request);
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

        $user = User::where('email', $email)->first();

        if (!$user) {
            // デフォルトロールを取得（WEB_USERを仮定）
            $defaultRoleId = \App\Models\Role::where('code', 'WEB_USER')->first()?->id ?? 1;
            $user = User::create([
                'name' => $supabaseUser['user_metadata']['name'] ?? $supabaseUser['email'],
                'email' => $email,
                'role_id' => $defaultRoleId,
            ]);
        }

        return $user;
    }
}

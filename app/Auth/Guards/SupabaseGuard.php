<?php

namespace App\Auth\Guards;

use App\Models\User;
use App\Services\SupabaseAuthService;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class SupabaseGuard implements Guard
{
    use GuardHelpers;

    protected Request $request;
    protected SupabaseAuthService $supabase;

    public function __construct(UserProvider $provider, Request $request, SupabaseAuthService $supabase)
    {
        $this->provider = $provider;
        $this->request = $request;
        $this->supabase = $supabase;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $token = $this->getTokenForRequest();

        if (empty($token)) {
            return null;
        }

        $supabaseUser = $this->supabase->verifyToken($token);

        if (empty($supabaseUser)) {
            return null;
        }

        $this->user = $this->syncUser($supabaseUser);

        return $this->user;
    }

    /**
     * Get the token for the current request.
     *
     * @return string|null
     */
    public function getTokenForRequest(): ?string
    {
        return $this->request->bearerToken();
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        // This guard is stateless and does not validate credentials directly.
        // Authentication is done via token verification.
        return false;
    }

    /**
     * Synchronize Supabase user with the local database.
     *
     * @param array $supabaseUser
     * @return User|null
     */
    protected function syncUser(array $supabaseUser): ?User
    {
        $uid = $supabaseUser['id'] ?? null;
        if (!$uid) {
            return null;
        }

        // Try to find user by Supabase UID first
        $user = $this->provider->retrieveByCredentials(['uid' => $uid]);

        if ($user) {
            return $user;
        }

        // If not found by UID, try by email
        $email = $supabaseUser['email'] ?? null;
        if (!$email) {
            return null;
        }

        /** @var User|null $user */
        $user = $this->provider->retrieveByCredentials(['email' => $email]);

        // If user exists, update their UID
        if ($user) {
            $user->uid = $uid;
            $user->save();
            return $user;
        }

        // If user does not exist at all, create them
        $defaultRoleId = \App\Models\Role::where('code', 'WEB_USER')->first()?->id;
        if(!$defaultRoleId) {
            // Fallback or handle error if role not found
            $defaultRoleId = 1; // Or throw an exception
        }

        return User::create([
            'uid' => $uid,
            'name' => $supabaseUser['user_metadata']['name'] ?? $supabaseUser['email'],
            'email' => $email,
            'role_id' => $defaultRoleId,
        ]);
    }
}

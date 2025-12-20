<?php

namespace App\Providers;

use App\Auth\Guards\SupabaseGuard;
use App\Services\SupabaseAuthService;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::extend('supabase', function ($app, $name, array $config) {
            return new SupabaseGuard(
                Auth::createUserProvider($config['provider']),
                $app->make('request'),
                $app->make(SupabaseAuthService::class)
            );
        });
    }
}

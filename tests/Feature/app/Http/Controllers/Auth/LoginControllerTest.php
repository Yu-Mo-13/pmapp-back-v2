<?php

namespace Tests\Feature\app\Http\Controllers\Auth;

use App\Services\SupabaseAuthService;
use Exception;
use Mockery;
use Tests\PmappTestCase;

class LoginControllerTest extends PmappTestCase
{
    public function test_ログインが成功すること(): void
    {
        $user = $this->webUser;
        $user->update([
            'email' => 'login-success@example.com',
            'uid' => 'uid-success',
        ]);
        $user->role->update([
            'top_page_url' => '/passwords',
        ]);

        $mock = Mockery::mock(SupabaseAuthService::class);
        $mock->shouldReceive('signIn')
            ->once()
            ->with('login-success@example.com', 'test-password')
            ->andReturn([
                'access_token' => 'test-token',
            ]);
        $this->app->instance(SupabaseAuthService::class, $mock);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'login-success@example.com',
            'password' => 'test-password',
        ]);

        $response->assertOk();
        $response->assertJson([
            'access_token' => 'test-token',
            'top_page_url' => '/passwords',
        ]);
    }

    public function test_バリデーションエラー時は422になること(): void
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email',
            'password',
        ]);
    }

    public function test_ユーザーが存在しない場合は422になること(): void
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => 'not-found@example.com',
            'password' => 'test-password',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'ログインに失敗しました。',
        ]);
    }

    public function test_supabase認証に失敗した場合は422になること(): void
    {
        $user = $this->webUser;
        $user->update([
            'email' => 'login-fail@example.com',
            'uid' => 'uid-fail',
        ]);

        $mock = Mockery::mock(SupabaseAuthService::class);
        $mock->shouldReceive('signIn')
            ->once()
            ->with('login-fail@example.com', 'test-password')
            ->andThrow(new Exception('Auth failed'));
        $this->app->instance(SupabaseAuthService::class, $mock);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'login-fail@example.com',
            'password' => 'test-password',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'ログインに失敗しました。',
        ]);
    }
}

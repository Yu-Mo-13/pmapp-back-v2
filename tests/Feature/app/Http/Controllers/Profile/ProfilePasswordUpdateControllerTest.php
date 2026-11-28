<?php

namespace Tests\Feature\app\Http\Controllers\Profile;

use App\Services\SupabaseAuthService;
use Exception;
use Mockery;
use Tests\PmappTestCase;

class ProfilePasswordUpdateControllerTest extends PmappTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->webUser->update([
            'email' => 'password-update@example.com',
            'uid' => 'uid-password-update',
        ]);
    }

    public function test_パスワードが変更できること(): void
    {
        $mock = Mockery::mock(SupabaseAuthService::class);
        $mock->shouldReceive('signIn')
            ->once()
            ->with('password-update@example.com', 'current-password')
            ->andReturn(['access_token' => 'reauth-token']);
        $mock->shouldReceive('updateUser')
            ->once()
            ->with('test-token', ['password' => 'new-password']);
        $this->app->instance(SupabaseAuthService::class, $mock);

        $this->actingAs($this->webUser, 'api');

        $response = $this->withHeader('Authorization', 'Bearer test-token')
            ->putJson(route('profile.password.update'), [
                'profile' => [
                    'current_password' => 'current-password',
                    'password' => 'new-password',
                    'password_confirmation' => 'new-password',
                ],
            ]);

        $response->assertOk();
    }

    public function test_現在のパスワードが誤っている場合は422になりパスワードは更新されないこと(): void
    {
        $mock = Mockery::mock(SupabaseAuthService::class);
        $mock->shouldReceive('signIn')
            ->once()
            ->andThrow(new Exception('Auth failed'));
        $mock->shouldNotReceive('updateUser');
        $this->app->instance(SupabaseAuthService::class, $mock);

        $this->actingAs($this->webUser, 'api');

        $response = $this->withHeader('Authorization', 'Bearer test-token')
            ->putJson(route('profile.password.update'), [
                'profile' => [
                    'current_password' => 'wrong-password',
                    'password' => 'new-password',
                    'password_confirmation' => 'new-password',
                ],
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => '現在のパスワードが正しくありません。',
        ]);
    }

    public function test_確認用パスワードが一致しない場合は422になること(): void
    {
        $this->actingAs($this->webUser, 'api');

        $response = $this->putJson(route('profile.password.update'), [
            'profile' => [
                'current_password' => 'current-password',
                'password' => 'new-password',
                'password_confirmation' => 'different-password',
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['profile.password']);
    }

    public function test_パスワードが短すぎる場合は422になること(): void
    {
        $this->actingAs($this->webUser, 'api');

        $response = $this->putJson(route('profile.password.update'), [
            'profile' => [
                'current_password' => 'current-password',
                'password' => 'short',
                'password_confirmation' => 'short',
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['profile.password']);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->putJson(route('profile.password.update'), [
            'profile' => [
                'current_password' => 'current-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ],
        ]);

        $response->assertStatus(401);
    }
}

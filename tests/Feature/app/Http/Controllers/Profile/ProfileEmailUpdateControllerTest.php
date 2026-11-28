<?php

namespace Tests\Feature\app\Http\Controllers\Profile;

use App\Services\SupabaseAuthService;
use Exception;
use Mockery;
use Tests\PmappTestCase;

class ProfileEmailUpdateControllerTest extends PmappTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->webUser->update([
            'email' => 'before@example.com',
            'uid' => 'uid-email-update',
        ]);
    }

    public function test_メールアドレス変更が申請できること(): void
    {
        $mock = Mockery::mock(SupabaseAuthService::class);
        $mock->shouldReceive('updateUser')
            ->once()
            ->with('test-token', ['email' => 'after@example.com']);
        $this->app->instance(SupabaseAuthService::class, $mock);

        $this->actingAs($this->webUser, 'api');

        $response = $this->withHeader('Authorization', 'Bearer test-token')
            ->putJson(route('profile.email.update'), [
                'profile' => [
                    'email' => 'after@example.com',
                ],
            ]);

        $response->assertOk();
        // 確認メールによる所有確認が完了するまではローカルDBは変更されない
        $this->assertDatabaseHas('users', [
            'id' => $this->webUser->id,
            'email' => 'before@example.com',
        ]);
    }

    public function test_メール形式が不正な場合は422になること(): void
    {
        $this->actingAs($this->webUser, 'api');

        $response = $this->putJson(route('profile.email.update'), [
            'profile' => [
                'email' => 'invalid-email',
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['profile.email']);
    }

    public function test_他のユーザーが使用中のメールアドレスは422になること(): void
    {
        $this->actingAs($this->webUser, 'api');

        $response = $this->putJson(route('profile.email.update'), [
            'profile' => [
                'email' => $this->adminUser->email,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['profile.email']);
    }

    public function test_supabase側の更新に失敗した場合は422になること(): void
    {
        $mock = Mockery::mock(SupabaseAuthService::class);
        $mock->shouldReceive('updateUser')
            ->once()
            ->andThrow(new Exception('Update failed'));
        $this->app->instance(SupabaseAuthService::class, $mock);

        $this->actingAs($this->webUser, 'api');

        $response = $this->withHeader('Authorization', 'Bearer test-token')
            ->putJson(route('profile.email.update'), [
                'profile' => [
                    'email' => 'after@example.com',
                ],
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'メールアドレスの変更に失敗しました。',
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->putJson(route('profile.email.update'), [
            'profile' => [
                'email' => 'after@example.com',
            ],
        ]);

        $response->assertStatus(401);
    }
}

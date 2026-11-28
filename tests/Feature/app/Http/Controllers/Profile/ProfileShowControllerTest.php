<?php

namespace Tests\Feature\app\Http\Controllers\Profile;

use App\Services\SupabaseAuthService;
use Mockery;
use Tests\PmappTestCase;

class ProfileShowControllerTest extends PmappTestCase
{
    public function test_自分のプロフィールが取得できること(): void
    {
        $this->actingAs($this->webUser, 'api');

        $response = $this->getJson(route('profile.show'));

        $response->assertOk();
        $response->assertJson([
            'name' => $this->webUser->name,
            'email' => $this->webUser->email,
            'role' => [
                'code' => 'WEB_USER',
                'name' => 'WEB一般ユーザー',
            ],
        ]);
    }

    public function test_未ログイン時は401になること(): void
    {
        $response = $this->getJson(route('profile.show'));

        $response->assertStatus(401);
    }

    public function test_supabase側のメールアドレスが変更されていたらローカルに同期されること(): void
    {
        $this->webUser->update([
            'email' => 'before@example.com',
            'uid' => 'uid-profile-sync',
        ]);

        $mock = Mockery::mock(SupabaseAuthService::class);
        $mock->shouldReceive('verifyToken')
            ->once()
            ->with('test-token')
            ->andReturn([
                'id' => 'uid-profile-sync',
                'email' => 'after@example.com',
            ]);
        $this->app->instance(SupabaseAuthService::class, $mock);

        $response = $this->withHeader('Authorization', 'Bearer test-token')
            ->getJson(route('profile.show'));

        $response->assertOk();
        $response->assertJson([
            'email' => 'after@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->webUser->id,
            'email' => 'after@example.com',
        ]);
    }
}

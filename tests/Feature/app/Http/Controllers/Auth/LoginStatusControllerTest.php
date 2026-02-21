<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PmappTestCase;

class LoginStatusControllerTest extends PmappTestCase
{
    use RefreshDatabase;

    public function test_show_returns_unauthenticated_for_guest()
    {
        // Add header to indicate json request
        $response = $this->getJson(route('auth.login.status'));

        $response->assertOk()
            ->assertJson([
                'name' => 'ゲスト',
            ]);
    }

    public function test_show_returns_user_info_for_authenticated_user()
    {
        $user = $this->webUser;
        $this->actingAs($user);

        $response = $this->getJson(route('auth.login.status'));

        $response->assertOk()
            ->assertJson([
                'name' => $user->name,
            ]);
    }

    public function test_show_returns_user_info_for_authenticated_admin_user()
    {
        $user = $this->adminUser;
        $this->actingAs($user);

        $response = $this->getJson(route('auth.login.status'));

        $response->assertOk()
            ->assertJson([
                'name' => $user->name,
            ]);
    }
}

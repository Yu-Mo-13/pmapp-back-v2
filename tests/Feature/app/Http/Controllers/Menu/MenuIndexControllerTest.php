<?php

namespace Tests\Feature\App\Http\Controllers\Menu;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\PmappTestCase;

class MenuIndexControllerTest extends PmappTestCase
{
    use RefreshDatabase;

    public function test_未ログイン時は空配列を返す(): void
    {
        $this->createMenus();

        $response = $this->getJson(route('menus'));

        $response->assertOk()
            ->assertExactJson([]);
    }

    public function test_一般ユーザーは全メニューを返す(): void
    {
        $this->createMenus();
        $this->actingAs($this->webUser);

        $response = $this->getJson(route('menus'));

        $response->assertOk()
            ->assertExactJson([
                ['name' => 'パスワード検索', 'path' => '/passwords'],
                ['name' => '未登録パスワード一覧', 'path' => '/unregisted-passwords'],
                ['name' => '仮登録パスワード一覧', 'path' => '/temp-passwords'],
            ]);
    }

    public function test_管理者は権限対象メニューのみ返す(): void
    {
        $this->createMenus();
        $this->actingAs($this->adminUser);

        $response = $this->getJson(route('menus'));

        $response->assertOk()
            ->assertExactJson([
                ['name' => 'アプリケーション一覧', 'path' => '/applications'],
                ['name' => 'アカウント一覧', 'path' => '/accounts'],
                ['name' => 'パスワード検索', 'path' => '/passwords'],
                ['name' => '未登録パスワード一覧', 'path' => '/unregisted-passwords'],
                ['name' => '仮登録パスワード一覧', 'path' => '/temp-passwords'],
            ]);
    }

    private function createMenus(): void
    {
        Menu::factory()->create([
            'name' => 'アプリケーション一覧',
            'path' => '/applications',
            'admin_visible' => true,
            'web_user_visible' => false,
            'mobile_user_visible' => false,
            'sort_order' => 1,
        ]);

        Menu::factory()->create([
            'name' => 'アカウント一覧',
            'path' => '/accounts',
            'admin_visible' => true,
            'web_user_visible' => false,
            'mobile_user_visible' => false,
            'sort_order' => 2,
        ]);

        Menu::factory()->create([
            'name' => 'パスワード検索',
            'path' => '/passwords',
            'admin_visible' => true,
            'web_user_visible' => true,
            'mobile_user_visible' => true,
            'sort_order' => 3,
        ]);

        Menu::factory()->create([
            'name' => '未登録パスワード一覧',
            'path' => '/unregisted-passwords',
            'admin_visible' => true,
            'web_user_visible' => true,
            'mobile_user_visible' => true,
            'sort_order' => 4,
        ]);

        Menu::factory()->create([
            'name' => '仮登録パスワード一覧',
            'path' => '/temp-passwords',
            'admin_visible' => true,
            'web_user_visible' => true,
            'mobile_user_visible' => true,
            'sort_order' => 5,
        ]);
    }
}

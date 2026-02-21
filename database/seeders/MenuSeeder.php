<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Menu::truncate();

        Menu::insert([
            [
                'name' => 'アプリケーション一覧',
                'path' => '/applications',
                'admin_visible' => true,
                'web_user_visible' => false,
                'mobile_user_visible' => false,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'アカウント一覧',
                'path' => '/accounts',
                'admin_visible' => true,
                'web_user_visible' => false,
                'mobile_user_visible' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'パスワード検索',
                'path' => '/passwords',
                'admin_visible' => true,
                'web_user_visible' => true,
                'mobile_user_visible' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '未登録パスワード一覧',
                'path' => '/unregisted-passwords',
                'admin_visible' => true,
                'web_user_visible' => true,
                'mobile_user_visible' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '仮登録パスワード一覧',
                'path' => '/temp-passwords',
                'admin_visible' => true,
                'web_user_visible' => true,
                'mobile_user_visible' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

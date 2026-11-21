<?php

namespace Tests\Feature;

use App\Models\Menu;
use Database\Seeders\MenuSeeder;
use Tests\TestCase;

class UnregistedPasswordMenuMigrationTest extends TestCase
{
    private const MIGRATION = '2026_11_21_140000_remove_unregisted_password_menu';

    public function test_既存の未登録メニューを削除し仮登録メニューを繰り上げる(): void
    {
        $this->seed(MenuSeeder::class);
        Menu::factory()->create(['path' => '/unregisted-passwords', 'sort_order' => 4]);
        $temporaryMenu = Menu::where('path', '/temp-passwords')->firstOrFail();
        $temporaryMenu->update(['sort_order' => 5]);

        // Re-run the data migration against the pre-release menu records.
        $this->app['migration.repository']->delete((object) ['migration' => self::MIGRATION]);
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);

        $this->assertDatabaseMissing('menus', ['path' => '/unregisted-passwords']);
        $this->assertDatabaseHas('menus', ['id' => $temporaryMenu->id, 'sort_order' => 4]);
        $this->assertDatabaseHas('menus', ['path' => '/passwords', 'sort_order' => 3]);
    }

    public function test_新規のメニューは仮登録メニューを4番目にシードする(): void
    {
        $this->seed(MenuSeeder::class);

        $this->assertDatabaseHas('menus', ['path' => '/temp-passwords', 'sort_order' => 4]);
    }
}

<?php

namespace Tests\Feature;

use Database\Seeders\MenuSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\PmappTestCase;

class UnregistedPasswordRemovalTest extends PmappTestCase
{
    /**
     * @dataProvider removedRoutes
     */
    public function test_未登録パスワードのAPIは存在しない(string $method, string $path): void
    {
        config(['services.supabase.url' => 'https://example.test', 'services.supabase.anon_key' => 'test']);
        $uuid = (string) Str::uuid();
        DB::table('unregisted_passwords')->insert([
            'uuid' => $uuid,
            'password' => 'test-password',
            'application_id' => 1,
            'account_id' => 1,
        ]);
        $path = str_replace('{uuid}', $uuid, $path);

        $this->actingAs($this->adminUser);
        $this->json($method, $path)->assertNotFound();
    }

    public function removedRoutes(): array
    {
        $base = '/api/v2/unregisted-passwords';

        return [
            ['GET', $base],
            ['GET', $base . '/{uuid}'],
            ['DELETE', $base . '/{uuid}'],
            ['DELETE', $base],
        ];
    }

    public function test_未登録パスワード一覧のメニューはシードされない(): void
    {
        $this->seed(MenuSeeder::class);
        $this->assertDatabaseMissing('menus', ['path' => '/unregisted-passwords']);
    }
}

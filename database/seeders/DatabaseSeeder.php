<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (!app()->isLocal()) {
            $this->call(MenuSeeder::class);

            return;
        }

        DB::transaction(function () {
            $this->call([
                MenuSeeder::class,
                RoleSeeder::class,
                UserSeeder::class,
                ApplicationSeeder::class,
                AccountSeeder::class,
                PasswordSeeder::class,
                PreregistedPasswordSeeder::class,
            ]);
        });
    }
}

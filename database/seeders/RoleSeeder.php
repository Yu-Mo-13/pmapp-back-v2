<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::factory()->admin()->create();
        Role::factory()->webUser()->create();
        Role::factory()->mobileUser()->create();
    }
}

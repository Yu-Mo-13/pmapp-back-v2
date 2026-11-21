<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Applicationモデルをインポート
use App\Models\Application;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Application::factory()->create(['account_class' => true]);
        Application::factory()->count(9)->create();
    }
}

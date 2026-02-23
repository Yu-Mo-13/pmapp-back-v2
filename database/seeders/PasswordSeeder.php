<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Application;
use App\Models\Password;
use Illuminate\Database\Seeder;

class PasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Password::truncate();

        if (Application::count() === 0) {
            Application::factory()->count(10)->create();
        }

        if (Account::count() === 0) {
            Account::factory()->count(30)->create();
        }

        $accounts = Account::query()->get();

        foreach ($accounts as $account) {
            Password::factory()->create([
                'application_id' => $account->application_id,
                'account_id' => $account->id,
            ]);
        }
    }
}

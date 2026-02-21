<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Application;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Account::factory()->count(30)->create([
            'application_id' => function () {
                $applications = $this->getApplicationsNeedAccount();
                return $applications->random()->id;
            },
        ]);
    }

    private function getApplicationsNeedAccount()
    {
        return Application::where('account_class', true)->get();
    }
}

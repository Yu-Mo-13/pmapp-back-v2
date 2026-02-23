<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Application;
use App\Models\Password;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasswordFactory extends Factory
{
    protected $model = Password::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'password' => $this->faker->password(8),
            'application_id' => Application::factory(),
            'account_id' => Account::factory(),
        ];
    }
}

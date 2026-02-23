<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Application;
use App\Models\PreregistedPassword;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PreregistedPasswordFactory extends Factory
{
    protected $model = PreregistedPassword::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => (string) Str::uuid(),
            'password' => $this->faker->password(8),
            'application_id' => Application::factory(),
            'account_id' => Account::factory(),
        ];
    }
}

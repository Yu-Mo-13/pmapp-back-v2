<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'code' => $this->faker->unique()->word,
        ];
    }

    public function admin()
    {
        return $this->state([
            'name' => 'システム管理者',
            'code' => 'ADMIN',
        ]);
    }

    public function webUser()
    {
        return $this->state([
            'name' => 'WEB一般ユーザー',
            'code' => 'WEB_USER',
        ]);
    }

    public function mobileUser()
    {
        return $this->state([
            'name' => 'Mobile一般ユーザー',
            'code' => 'MOBILE_USER',
        ]);
    }
}

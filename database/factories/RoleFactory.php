<?php

namespace Database\Factories;

use App\Http\Enums\Role\RoleEnum;
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
            'name' => RoleEnum::getDescription(RoleEnum::ADMIN),
            'code' => RoleEnum::ADMIN,
        ]);
    }

    public function webUser()
    {
        return $this->state([
            'name' => RoleEnum::getDescription(RoleEnum::WEB_USER),
            'code' => RoleEnum::WEB_USER,
        ]);
    }

    public function mobileUser()
    {
        return $this->state([
            'name' => RoleEnum::getDescription(RoleEnum::MOBILE_USER),
            'code' => RoleEnum::MOBILE_USER,
        ]);
    }
}

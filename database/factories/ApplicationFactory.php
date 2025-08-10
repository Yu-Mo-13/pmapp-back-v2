<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
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
            'account_class' => $this->faker->boolean,
            'notice_class' => $this->faker->boolean,
            'mark_class' => $this->faker->boolean,
            'pre_password_size' => $this->faker->numberBetween(8, 16),
        ];
    }
}

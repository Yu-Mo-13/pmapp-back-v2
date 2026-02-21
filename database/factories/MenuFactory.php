<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'path' => '/' . $this->faker->unique()->slug,
            'admin_visible' => false,
            'web_user_visible' => false,
            'mobile_user_visible' => false,
            'sort_order' => $this->faker->numberBetween(1, 99),
        ];
    }
}

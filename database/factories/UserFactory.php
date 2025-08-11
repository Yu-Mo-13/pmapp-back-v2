<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Role;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $roles = Role::all()->pluck('id')->toArray();
        if (empty($roles)) {
            // デフォルトのロールが存在しない場合は、ロールファクトリを使用して作成
            throw new \Exception('No roles found. Please create roles before creating users.');
        }

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'role_id' => $this->faker->randomElement($roles),
        ];
    }
}

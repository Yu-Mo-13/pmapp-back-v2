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
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'role_id' => function () {
                // ロールが存在しない場合は作成する
                return Role::factory()->create()->id;
            },
        ];
    }

    /**
     * 特定のロールでユーザーを作成する
     */
    public function withRole($roleId)
    {
        return $this->state([
            'role_id' => $roleId,
        ]);
    }
}

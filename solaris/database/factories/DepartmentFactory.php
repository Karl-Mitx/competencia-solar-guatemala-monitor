<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('??')),
            'name' => fake()->unique()->city(),
            'latitude' => fake()->latitude(13.7, 15.9),
            'longitude' => fake()->longitude(-92.2, -88.2),
            'color' => fake()->hexColor(),
        ];
    }
}

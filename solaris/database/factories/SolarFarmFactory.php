<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\SolarFarm>
 */
class SolarFarmFactory extends Factory
{
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => fake()->company().' Solar',
            'location_name' => fake()->city(),
            'latitude' => fake()->latitude(13.7, 15.9),
            'longitude' => fake()->longitude(-92.2, -88.2),
            'families_count' => fake()->numberBetween(50, 2000),
            'is_active' => true,
            'commissioned_at' => fake()->dateTimeBetween('-5 years', '-6 months'),
            'notes' => null,
            'photo_path' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}

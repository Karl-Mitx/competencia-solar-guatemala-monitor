<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\SolarPanel>
 */
class SolarPanelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'brand' => fake()->randomElement(['SunPower', 'LG Solar', 'Jinko Solar', 'Canadian Solar', 'REC Group']),
            'model' => strtoupper(fake()->lexify('??-???')).'-'.fake()->numberBetween(300, 600),
            'nominal_power_kw' => fake()->randomFloat(3, 0.300, 0.600),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}

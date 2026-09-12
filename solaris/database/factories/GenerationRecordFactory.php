<?php

namespace Database\Factories;

use App\Models\SolarFarm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\GenerationRecord>
 */
class GenerationRecordFactory extends Factory
{
    public function definition(): array
    {
        $expected = fake()->randomFloat(2, 500, 50000);
        $ratio = fake()->randomFloat(2, 0.60, 1.20);

        return [
            'solar_farm_id' => SolarFarm::factory(),
            'period' => fake()->dateTimeBetween('-24 months', 'now')->format('Y-m-01'),
            'real_kwh' => round($expected * $ratio, 2),
            'expected_kwh' => $expected,
        ];
    }

    public function underperforming(): static
    {
        return $this->state(function (array $attributes) {
            $expected = $attributes['expected_kwh'] ?? fake()->randomFloat(2, 500, 50000);

            return [
                'expected_kwh' => $expected,
                'real_kwh' => round($expected * fake()->randomFloat(2, 0.40, 0.79), 2),
            ];
        });
    }
}

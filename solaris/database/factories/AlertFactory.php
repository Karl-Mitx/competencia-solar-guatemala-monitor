<?php

namespace Database\Factories;

use App\Models\GenerationRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Alert>
 */
class AlertFactory extends Factory
{
    public function definition(): array
    {
        return [
            'generation_record_id' => GenerationRecord::factory()->underperforming(),
            'status' => 'active',
            'resolved_at' => null,
        ];
    }

    public function resolved(): static
    {
        return $this->state([
            'status' => 'resolved',
            'resolved_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }
}

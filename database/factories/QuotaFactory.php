<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quota>
 */
class QuotaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            // @phpstan-ignore-next-line
            'timestamp' => $this->faker
                                ->timeSeries('yesterday 12:00', '1 minute'),
            // 'state_of_charge' => fake()->optional()->numberBetween(0, 100),
            'state_of_charge' => fake()->numberBetween(0, 100),
            // 'watts_out_sum' => fake()->optional()->numberBetween(0, 600),
            'watts_out_sum' => fake()->numberBetween(0, 600),
            // 'watts_in_sum' => fake()->optional()->numberBetween(0, 200),
            'watts_in_sum' => fake()->numberBetween(0, 200),
        ];
    }
}

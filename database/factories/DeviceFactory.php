<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'serial_number' => fake()->unique()->regexify('[A-Z0-9]{15}'),
            'name' => fake()->optional()->emoji(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'currency' => fake()->optional()->currencyCode(),
            'investment' => fake()->optional()->randomFloat(2, 0, 999_999.99),
            'price_per_kilowatt_hour' => fake()->optional()->randomFloat(4, 0, 9.9999),
        ];
    }
}

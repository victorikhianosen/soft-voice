<?php

namespace Database\Factories;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estimate>
 */
class EstimateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => fake()->numberBetween(1, 5),
            'estimate_id' => fake()->unique()->randomNumber(6),
            'customer_id' => fake()->numberBetween(1, 200),
            'date' => fake()->date(),
            'due_date' => fake()->date(),
            'currency' => fake()->currencyCode(),
            'tax_enabled' => fake()->boolean(),
            'status' => fake()->randomElement(Status::types()),
        ];
    }
}

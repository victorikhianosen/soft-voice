<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EstimateItem>
 */
class EstimateItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'estimate_id' => fake()->numberBetween(1, 200),
            'name' => fake()->word(),
            'quantity' => fake()->randomNumber(),
            'price' => fake()->randomNumber(5),
            'amount' => fake()->randomNumber(5),
            'discount' => fake()->randomNumber(2),
        ];
    }
}

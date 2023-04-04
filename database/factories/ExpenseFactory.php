<?php

namespace Database\Factories;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
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
            'customer_id' => fake()->numberBetween(1, 200),
            'date' => fake()->date(),
            'amount' => fake()->randomNumber(6),
            'recurring' => fake()->boolean(),
            'status' => fake()->randomElement(Status::types()),
        ];
    }
}

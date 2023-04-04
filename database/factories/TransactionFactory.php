<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
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
            'type' => fake()->randomElement(['deposit', 'withdrawal', 'others']),
            'amount' => fake()->randomNumber(6),
            'description' => fake()->sentence(),
            'method' => fake()->randomElement(['wallet', 'card', 'deposit']),
            'status' => fake()->randomElement(['pending', 'approved', 'declined']),
        ];
    }
}

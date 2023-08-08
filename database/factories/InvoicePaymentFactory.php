<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoicePayment>
 */
class InvoicePaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'invoice_id' => fake()->numberBetween(1, 200),
            'date' => fake()->date(),
            'method' => fake()->randomElement(['transfer', 'cash']),
            'amount' => fake()->randomNumber(5),
            'notify_client' => fake()->boolean(2),
        ];
    }
}

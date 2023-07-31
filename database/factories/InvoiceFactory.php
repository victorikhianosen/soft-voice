<?php

namespace Database\Factories;

use App\Enums\Status;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
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
            'invoice_id' => fake()->unique()->randomNumber(6),
            'customer_id' => fake()->numberBetween(1, 200),
            'date' => fake()->date(),
            'due_date' => fake()->date(),
            'currency' => fake()->currencyCode(),
            'code' => Str::random(10),
            'tax_enabled' => fake()->boolean(),
            'recurring' => fake()->boolean(),
            'currency' => fake()->currencyCode(),
            'status' => fake()->randomElement(Status::types()),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code' => fake()->unique()->word(),
            'discount' => fake()->numberBetween(0, 100),
            'total' => fake()->numberBetween(0, 100),
            'expiry_date' => fake()->date(),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => 'single',
            'to' => fake()->email(),
            'subject' => fake()->sentence(),
            'body' => fake()->sentence(),
            'notification' => fake()->boolean(),
            'status' => fake()->randomElement(['sending', 'success', 'failed']),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Enums\InvoiceStatus;

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
    public function definition(): array
    {
        return [
            'user_id' => User::query()->first(),
            'status' => InvoiceStatus::Outstanding,
            'description' => fake()->sentence,
            'amount' => 10000,
            'date' => now(),
        ];
    }

    /**
     *  Instantiate the model with a paid status
     *
     * @return $this
     */
    public function paid(): static
    {
        return $this->state(fn ($attributes) => [
            'status' => InvoiceStatus::Paid,
        ]);
    }

    /**
     * Instantiate the model with a void status
     *
     * @return $this
     */
    public function void(): static
    {
        return $this->state(fn ($attributes) => [
            'status' => InvoiceStatus::Void,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Enums\MembershipStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::query()->first()->id,
            'status' => MembershipStatus::Active,
            'credits' => 10,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ];
    }
}

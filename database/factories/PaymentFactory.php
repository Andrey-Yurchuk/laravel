<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        /** @var \Database\Factories\UserFactory $userFactory */
        $userFactory = User::factory();

        return [
            'subscription_id' => Subscription::factory(),
            'user_id' => $userFactory->student(),
            'amount' => fake()->randomFloat(2, 10, 100),
            'status' => PaymentStatus::Pending,
            'transaction_id' => null,
            'paid_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Pending,
            'transaction_id' => null,
            'paid_at' => null,
        ]);
    }

    public function succeeded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Succeeded,
            'transaction_id' => fake()->uuid(),
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Failed,
            'transaction_id' => null,
            'paid_at' => null,
        ]);
    }
}

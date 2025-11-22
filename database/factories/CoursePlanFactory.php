<?php

namespace Database\Factories;

use App\Enums\CoursePlanStatus;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoursePlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->paragraph(),
            'price_monthly' => fake()->randomFloat(2, 10, 100),
            'price_yearly' => fake()->randomFloat(2, 100, 1000),
            'status' => CoursePlanStatus::Active,
        ];
    }

    public function basic(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Basic Access',
            'description' => 'Basic access to course materials',
            'price_monthly' => 19.99,
            'price_yearly' => 199.99,
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Premium Access',
            'description' => 'Premium access with all features',
            'price_monthly' => 29.99,
            'price_yearly' => 299.99,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CoursePlanStatus::Active,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CoursePlanStatus::Inactive,
        ]);
    }
}

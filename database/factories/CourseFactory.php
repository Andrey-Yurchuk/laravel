<?php

namespace Database\Factories;

use App\Enums\CourseDifficulty;
use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(3);

        /** @var \Database\Factories\UserFactory $userFactory */
        $userFactory = User::factory();

        return [
            'instructor_id' => $userFactory->instructor(),
            'category_id' => Category::factory(),
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'description' => fake()->paragraphs(3, true),
            'difficulty_level' => CourseDifficulty::Beginner,
            'status' => CourseStatus::Draft,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseStatus::Published,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseStatus::Draft,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseStatus::Archived,
        ]);
    }

    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty_level' => CourseDifficulty::Beginner,
        ]);
    }

    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty_level' => CourseDifficulty::Intermediate,
        ]);
    }

    public function advanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty_level' => CourseDifficulty::Advanced,
        ]);
    }
}

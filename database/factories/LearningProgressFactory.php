<?php

namespace Database\Factories;

use App\Enums\LessonProgressStatus;
use App\Models\Lesson;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LearningProgressFactory extends Factory
{
    public function definition(): array
    {
        /** @var \Database\Factories\UserFactory $userFactory */
        $userFactory = User::factory();

        return [
            'user_id' => $userFactory->student(),
            'subscription_id' => Subscription::factory(),
            'lesson_id' => Lesson::factory(),
            'status' => LessonProgressStatus::NotStarted,
            'completed_at' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LessonProgressStatus::InProgress,
            'completed_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LessonProgressStatus::Completed,
            'completed_at' => now(),
        ]);
    }
}

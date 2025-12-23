<?php

namespace Tests\Feature\Middleware;

use App\Enums\SubscriptionStatus;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EnsureActiveSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['auth', 'subscription.active'])
            ->get('/protected-course/{course}', fn (Course $course) => 'OK');

        Route::middleware(['auth', 'subscription.active'])
            ->get('/protected-lesson/{lesson}', fn (Lesson $lesson) => 'OK');
    }

    public function test_allows_access_with_active_subscription(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create();
        Subscription::factory()
            ->active()
            ->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => SubscriptionStatus::Active,
            ]);

        $response = $this->actingAs($user)->get("/protected-course/{$course->id}");

        $response->assertOk();
    }

    public function test_denies_access_without_subscription(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create();

        $response = $this->actingAs($user)->get("/protected-course/{$course->id}");

        $response->assertStatus(403);
    }

    public function test_denies_access_with_expired_subscription(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::factory()->create();
        Subscription::factory()
            ->expired()
            ->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => SubscriptionStatus::Expired,
            ]);

        $response = $this->actingAs($user)->get("/protected-course/{$course->id}");

        $response->assertStatus(403);
    }

    public function test_allows_preview_lessons_without_subscription(): void
    {
        $user = User::factory()->student()->create();
        $lesson = Lesson::factory()->preview()->create();

        $response = $this->actingAs($user)->get("/protected-lesson/{$lesson->id}");

        $response->assertOk();
    }
}

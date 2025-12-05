<?php

namespace Tests\Unit\Models;

use App\Enums\CourseDifficulty;
use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CoursePlan;
use App\Models\Lesson;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_can_be_created(): void
    {
        $course = Course::factory()->create([
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'Test description',
        ]);

        $this->assertInstanceOf(Course::class, $course);
        $this->assertEquals('Test Course', $course->title);
        $this->assertEquals('test-course', $course->slug);
        $this->assertEquals('Test description', $course->description);
    }

    public function test_difficulty_level_is_casted_to_enum(): void
    {
        $course = Course::factory()->create([
            'difficulty_level' => CourseDifficulty::Intermediate,
        ]);

        $this->assertInstanceOf(CourseDifficulty::class, $course->difficulty_level);
        $this->assertEquals(CourseDifficulty::Intermediate, $course->difficulty_level);
    }

    public function test_status_is_casted_to_enum(): void
    {
        $course = Course::factory()->create([
            'status' => CourseStatus::Published,
        ]);

        $this->assertInstanceOf(CourseStatus::class, $course->status);
        $this->assertEquals(CourseStatus::Published, $course->status);
    }

    public function test_course_has_instructor_relationship(): void
    {
        $course = Course::factory()->create();
        $relationship = $course->instructor();

        $this->assertInstanceOf(BelongsTo::class, $relationship);
        $this->assertEquals(User::class, $relationship->getRelated()::class);
        $this->assertEquals('instructor_id', $relationship->getForeignKeyName());
    }

    public function test_course_has_category_relationship(): void
    {
        $course = Course::factory()->create();
        $relationship = $course->category();

        $this->assertInstanceOf(BelongsTo::class, $relationship);
        $this->assertEquals(Category::class, $relationship->getRelated()::class);
    }

    public function test_course_has_lessons_relationship(): void
    {
        $course = Course::factory()->create();
        $relationship = $course->lessons();

        $this->assertInstanceOf(HasMany::class, $relationship);
        $this->assertEquals(Lesson::class, $relationship->getRelated()::class);
    }

    public function test_course_has_plans_relationship(): void
    {
        $course = Course::factory()->create();
        $relationship = $course->plans();

        $this->assertInstanceOf(HasMany::class, $relationship);
        $this->assertEquals(CoursePlan::class, $relationship->getRelated()::class);
    }

    public function test_course_has_subscriptions_relationship(): void
    {
        $course = Course::factory()->create();
        $relationship = $course->subscriptions();

        $this->assertInstanceOf(HasMany::class, $relationship);
        $this->assertEquals(Subscription::class, $relationship->getRelated()::class);
    }
}


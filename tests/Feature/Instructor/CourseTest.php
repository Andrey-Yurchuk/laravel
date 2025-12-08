<?php

namespace Tests\Feature\Instructor;

use App\Enums\CourseDifficulty;
use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private User $otherInstructor;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->instructor = User::factory()->instructor()->create();
        $this->otherInstructor = User::factory()->instructor()->create();
        $this->category = Category::factory()->create();
    }

    public function test_guest_cannot_access_courses_index(): void
    {
        $response = $this->get(route('instructor.courses.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_courses_index(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get(route('instructor.courses.index'));

        $response->assertForbidden();
    }

    public function test_instructor_can_view_own_courses_index(): void
    {
        Course::factory()->count(2)->create([
            'instructor_id' => $this->instructor->id,
        ]);
        Course::factory()->create([
            'instructor_id' => $this->otherInstructor->id,
        ]);

        $response = $this->actingAs($this->instructor)->get(route('instructor.courses.index'));

        $response->assertStatus(200);
        $response->assertViewIs('instructor.courses.index');
    }

    public function test_instructor_can_view_create_course_form(): void
    {
        $response = $this->actingAs($this->instructor)->get(route('instructor.courses.create'));

        $response->assertStatus(200);
        $response->assertViewIs('instructor.courses.create');
    }

    public function test_instructor_can_create_course(): void
    {
        $data = [
            'category_id' => $this->category->id,
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('instructor.courses.store'), $data);

        $this->assertDatabaseHas('courses', [
            'title' => 'Test Course',
            'slug' => 'test-course',
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect(route('instructor.courses.index'));
        $response->assertSessionHas('success');
    }

    public function test_instructor_cannot_create_course_without_title(): void
    {
        $data = [
            'category_id' => $this->category->id,
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->instructor)
            ->from(route('instructor.courses.create'))
            ->post(route('instructor.courses.store'), $data);

        $response->assertSessionHasErrors('title');
        $response->assertRedirect(route('instructor.courses.create'));
    }

    public function test_instructor_cannot_create_course_with_duplicate_slug(): void
    {
        Course::factory()->create(['slug' => 'existing-slug']);

        $data = [
            'category_id' => $this->category->id,
            'title' => 'Test Course',
            'slug' => 'existing-slug',
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->instructor)
            ->from(route('instructor.courses.create'))
            ->post(route('instructor.courses.store'), $data);

        $response->assertSessionHasErrors('slug');
        $response->assertRedirect(route('instructor.courses.create'));
    }

    public function test_instructor_can_view_own_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('instructor.courses.show', $course->id));

        $response->assertStatus(200);
        $response->assertViewIs('instructor.courses.show');
    }

    public function test_instructor_cannot_view_other_instructor_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->otherInstructor->id,
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('instructor.courses.show', $course->id));

        $response->assertForbidden();
    }

    public function test_instructor_can_view_edit_own_course_form(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('instructor.courses.edit', $course->id));

        $response->assertStatus(200);
        $response->assertViewIs('instructor.courses.edit');
    }

    public function test_instructor_cannot_view_edit_other_instructor_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->otherInstructor->id,
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('instructor.courses.edit', $course->id));

        $response->assertForbidden();
    }

    public function test_instructor_can_update_own_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'title' => 'Old Title',
            'slug' => 'old-slug',
        ]);

        $data = [
            'category_id' => $this->category->id,
            'title' => 'New Title',
            'slug' => 'new-slug',
            'description' => 'New description',
            'difficulty_level' => CourseDifficulty::Advanced->value,
            'status' => CourseStatus::Archived->value,
        ];

        $response = $this->actingAs($this->instructor)
            ->put(route('instructor.courses.update', $course->id), $data);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'New Title',
            'slug' => 'new-slug',
            'instructor_id' => $this->instructor->id,
        ]);

        $response->assertRedirect(route('instructor.courses.index'));
        $response->assertSessionHas('success');
    }

    public function test_instructor_cannot_update_other_instructor_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->otherInstructor->id,
        ]);

        $data = [
            'category_id' => $this->category->id,
            'title' => 'New Title',
            'description' => 'New description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->instructor)
            ->put(route('instructor.courses.update', $course->id), $data);

        $response->assertForbidden();
    }

    public function test_instructor_cannot_delete_own_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
        ]);

        $response = $this->actingAs($this->instructor)
            ->delete(route('instructor.courses.destroy', $course->id));

        $response->assertForbidden();
        
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
        ]);
    }

    public function test_instructor_cannot_delete_other_instructor_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->otherInstructor->id,
        ]);

        $response = $this->actingAs($this->instructor)
            ->delete(route('instructor.courses.destroy', $course->id));

        $response->assertForbidden();
    }

    public function test_student_cannot_create_course(): void
    {
        $student = User::factory()->student()->create();

        $data = [
            'category_id' => $this->category->id,
            'title' => 'Test Course',
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($student)
            ->post(route('instructor.courses.store'), $data);

        $response->assertForbidden();
    }
}


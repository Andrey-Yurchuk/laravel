<?php

namespace Tests\Feature\Admin;

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

    private User $admin;
    private User $instructor;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->instructor = User::factory()->instructor()->create();
        $this->category = Category::factory()->create();
    }

    public function test_guest_cannot_access_courses_index(): void
    {
        $response = $this->get(route('admin.courses.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_student_can_access_courses_index_but_cannot_create(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get(route('admin.courses.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_courses_index(): void
    {
        Course::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.courses.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.courses.index');
    }

    public function test_admin_can_view_create_course_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.courses.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.courses.create');
    }

    public function test_admin_can_create_course(): void
    {
        $data = [
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.courses.store'), $data);

        $this->assertDatabaseHas('courses', [
            'title' => 'Test Course',
            'slug' => 'test-course',
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $response->assertSessionHas('success');
    }

    public function test_admin_cannot_create_course_without_title(): void
    {
        $data = [
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.courses.create'))
            ->post(route('admin.courses.store'), $data);

        $response->assertSessionHasErrors('title');
        $response->assertRedirect(route('admin.courses.create'));
    }

    public function test_admin_cannot_create_course_without_instructor(): void
    {
        $data = [
            'category_id' => $this->category->id,
            'title' => 'Test Course',
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.courses.create'))
            ->post(route('admin.courses.store'), $data);

        $response->assertSessionHasErrors('instructor_id');
        $response->assertRedirect(route('admin.courses.create'));
    }

    public function test_admin_cannot_create_course_with_invalid_instructor(): void
    {
        $data = [
            'instructor_id' => 99999,
            'category_id' => $this->category->id,
            'title' => 'Test Course',
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.courses.create'))
            ->post(route('admin.courses.store'), $data);

        $response->assertSessionHasErrors('instructor_id');
        $response->assertRedirect(route('admin.courses.create'));
    }

    public function test_admin_cannot_create_course_with_duplicate_slug(): void
    {
        Course::factory()->create(['slug' => 'existing-slug']);

        $data = [
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Test Course',
            'slug' => 'existing-slug',
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.courses.create'))
            ->post(route('admin.courses.store'), $data);

        $response->assertSessionHasErrors('slug');
        $response->assertRedirect(route('admin.courses.create'));
    }

    public function test_admin_can_view_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.courses.show', $course->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.courses.show');
    }

    public function test_admin_can_view_edit_course_form(): void
    {
        $course = Course::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.courses.edit', $course->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.courses.edit');
    }

    public function test_admin_can_update_course(): void
    {
        $course = Course::factory()->create([
            'title' => 'Old Title',
            'slug' => 'old-slug',
        ]);

        $data = [
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'New Title',
            'slug' => 'new-slug',
            'description' => 'New description',
            'difficulty_level' => CourseDifficulty::Advanced->value,
            'status' => CourseStatus::Archived->value,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.courses.update', $course->id), $data);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'New Title',
            'slug' => 'new-slug',
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $response->assertSessionHas('success');
    }

    public function test_admin_cannot_update_course_without_title(): void
    {
        $course = Course::factory()->create();

        $data = [
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.courses.edit', $course->id))
            ->put(route('admin.courses.update', $course->id), $data);

        $response->assertSessionHasErrors('title');
        $response->assertRedirect(route('admin.courses.edit', $course->id));
    }

    public function test_admin_can_delete_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.courses.destroy', $course->id));

        $this->assertDatabaseMissing('courses', [
            'id' => $course->id,
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $response->assertSessionHas('success');
    }

    public function test_non_admin_cannot_create_course(): void
    {
        $data = [
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Test Course',
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('admin.courses.store'), $data);

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_update_course(): void
    {
        $course = Course::factory()->create();

        $data = [
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'New Title',
            'description' => 'Test description',
            'difficulty_level' => CourseDifficulty::Beginner->value,
            'status' => CourseStatus::Published->value,
        ];

        $response = $this->actingAs($this->instructor)
            ->put(route('admin.courses.update', $course->id), $data);

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_delete_course(): void
    {
        $course = Course::factory()->create();
        $courseId = $course->id;

        $response = $this->actingAs($this->instructor)
            ->delete(route('admin.courses.destroy', $courseId));

        $response->assertForbidden();
        
        $this->assertDatabaseHas('courses', [
            'id' => $courseId,
        ]);
    }
}


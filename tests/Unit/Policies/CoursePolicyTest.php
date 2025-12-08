<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use App\Policies\CoursePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursePolicyTest extends TestCase
{
    use RefreshDatabase;

    private CoursePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CoursePolicy();
    }

    public function test_view_any_allows_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($this->policy->viewAny($admin));
    }

    public function test_view_any_allows_instructor(): void
    {
        $instructor = User::factory()->instructor()->create();

        $this->assertTrue($this->policy->viewAny($instructor));
    }

    public function test_view_any_denies_student(): void
    {
        $student = User::factory()->student()->create();

        $this->assertFalse($this->policy->viewAny($student));
    }

    public function test_view_allows_admin_for_any_course(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $this->assertTrue($this->policy->view($admin, $course));
    }

    public function test_view_allows_instructor_for_own_course(): void
    {
        $instructor = User::factory()->instructor()->create();
        $course = Course::factory()->create([
            'instructor_id' => $instructor->id,
        ]);

        $this->assertTrue($this->policy->view($instructor, $course));
    }

    public function test_view_denies_instructor_for_other_course(): void
    {
        $instructor = User::factory()->instructor()->create();
        $otherInstructor = User::factory()->instructor()->create();
        $course = Course::factory()->create([
            'instructor_id' => $otherInstructor->id,
        ]);

        $this->assertFalse($this->policy->view($instructor, $course));
    }

    public function test_view_denies_student(): void
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();

        $this->assertFalse($this->policy->view($student, $course));
    }

    public function test_create_allows_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($this->policy->create($admin));
    }

    public function test_create_allows_instructor(): void
    {
        $instructor = User::factory()->instructor()->create();

        $this->assertTrue($this->policy->create($instructor));
    }

    public function test_create_denies_student(): void
    {
        $student = User::factory()->student()->create();

        $this->assertFalse($this->policy->create($student));
    }

    public function test_update_allows_admin_for_any_course(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $this->assertTrue($this->policy->update($admin, $course));
    }

    public function test_update_allows_instructor_for_own_course(): void
    {
        $instructor = User::factory()->instructor()->create();
        $course = Course::factory()->create([
            'instructor_id' => $instructor->id,
        ]);

        $this->assertTrue($this->policy->update($instructor, $course));
    }

    public function test_update_denies_instructor_for_other_course(): void
    {
        $instructor = User::factory()->instructor()->create();
        $otherInstructor = User::factory()->instructor()->create();
        $course = Course::factory()->create([
            'instructor_id' => $otherInstructor->id,
        ]);

        $this->assertFalse($this->policy->update($instructor, $course));
    }

    public function test_update_denies_student(): void
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();

        $this->assertFalse($this->policy->update($student, $course));
    }

    public function test_delete_allows_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $this->assertTrue($this->policy->delete($admin, $course));
    }

    public function test_delete_denies_instructor(): void
    {
        $instructor = User::factory()->instructor()->create();
        $course = Course::factory()->create([
            'instructor_id' => $instructor->id,
        ]);

        $this->assertFalse($this->policy->delete($instructor, $course));
    }

    public function test_delete_denies_student(): void
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();

        $this->assertFalse($this->policy->delete($student, $course));
    }

    public function test_restore_allows_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $this->assertTrue($this->policy->restore($admin, $course));
    }

    public function test_restore_denies_instructor(): void
    {
        $instructor = User::factory()->instructor()->create();
        $course = Course::factory()->create();

        $this->assertFalse($this->policy->restore($instructor, $course));
    }

    public function test_force_delete_allows_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $this->assertTrue($this->policy->forceDelete($admin, $course));
    }

    public function test_force_delete_denies_instructor(): void
    {
        $instructor = User::factory()->instructor()->create();
        $course = Course::factory()->create();

        $this->assertFalse($this->policy->forceDelete($instructor, $course));
    }
}


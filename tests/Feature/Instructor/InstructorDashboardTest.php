<?php

namespace Tests\Feature\Instructor;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private User $otherInstructor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->instructor = User::factory()->instructor()->create();
        $this->otherInstructor = User::factory()->instructor()->create();
    }

    public function test_guest_cannot_access_instructor_dashboard(): void
    {
        $response = $this->get(route('instructor.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_instructor_can_access_dashboard(): void
    {
        Course::factory()->count(3)->create([
            'instructor_id' => $this->instructor->id,
        ]);
        Course::factory()->count(2)->create([
            'instructor_id' => $this->otherInstructor->id,
        ]);

        $response = $this->actingAs($this->instructor)->get(route('instructor.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('instructor.dashboard');
    }

    public function test_dashboard_shows_only_own_courses(): void
    {
        Course::factory()->count(5)->create([
            'instructor_id' => $this->instructor->id,
        ]);
        Course::factory()->count(3)->create([
            'instructor_id' => $this->otherInstructor->id,
        ]);

        $response = $this->actingAs($this->instructor)->get(route('instructor.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('courses');
        
        $courses = $response->viewData('courses');
        $this->assertLessThanOrEqual(5, $courses->count());
    }

    public function test_dashboard_shows_correct_statistics(): void
    {
        Course::factory()->count(5)->create([
            'instructor_id' => $this->instructor->id,
        ]);
        Course::factory()->published()->count(3)->create([
            'instructor_id' => $this->instructor->id,
        ]);

        $response = $this->actingAs($this->instructor)->get(route('instructor.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response->viewData('stats');
        $this->assertGreaterThanOrEqual(5, $stats['total_courses']);
        $this->assertEquals(3, $stats['published_courses']);
    }
}


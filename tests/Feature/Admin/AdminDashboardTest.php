<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_dashboard(): void
    {
        Category::factory()->count(3)->create();
        Course::factory()->count(5)->create();
        Course::factory()->published()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    public function test_dashboard_shows_correct_statistics(): void
    {
        $initialCategoriesCount = Category::count();
        $initialCoursesCount = Course::count();
        $initialPublishedCount = Course::where('status', 'published')->count();

        Category::factory()->count(5)->create();
        Course::factory()->count(10)->create();
        Course::factory()->published()->count(7)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response->viewData('stats');
        $this->assertGreaterThanOrEqual($initialCategoriesCount + 5, $stats['categories_count']);
        $this->assertGreaterThanOrEqual($initialCoursesCount + 10, $stats['courses_count']);
        $this->assertGreaterThanOrEqual($initialPublishedCount + 7, $stats['published_courses']);
    }
}


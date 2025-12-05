<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\LearningProgress;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => UserRole::Student,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals(UserRole::Student, $user->role);
    }

    public function test_password_is_hashed_when_set(): void
    {
        $user = User::factory()->create([
            'password' => 'plain-password',
        ]);

        $this->assertNotEquals('plain-password', $user->password);
        $this->assertTrue(Hash::check('plain-password', $user->password));
    }

    public function test_role_is_casted_to_enum(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Instructor,
        ]);

        $this->assertInstanceOf(UserRole::class, $user->role);
        $this->assertEquals(UserRole::Instructor, $user->role);
    }

    public function test_user_has_courses_relationship(): void
    {
        $user = User::factory()->create();
        $relationship = $user->courses();

        $this->assertInstanceOf(HasMany::class, $relationship);
        $this->assertEquals(Course::class, $relationship->getRelated()::class);
        $this->assertEquals('instructor_id', $relationship->getForeignKeyName());
    }

    public function test_user_has_subscriptions_relationship(): void
    {
        $user = User::factory()->create();
        $relationship = $user->subscriptions();

        $this->assertInstanceOf(HasMany::class, $relationship);
        $this->assertEquals(Subscription::class, $relationship->getRelated()::class);
    }

    public function test_user_has_payments_relationship(): void
    {
        $user = User::factory()->create();
        $relationship = $user->payments();

        $this->assertInstanceOf(HasMany::class, $relationship);
        $this->assertEquals(Payment::class, $relationship->getRelated()::class);
    }

    public function test_user_has_learning_progress_relationship(): void
    {
        $user = User::factory()->create();
        $relationship = $user->learningProgress();

        $this->assertInstanceOf(HasMany::class, $relationship);
        $this->assertEquals(LearningProgress::class, $relationship->getRelated()::class);
    }

    public function test_get_dashboard_route_returns_admin_route_for_admin(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertEquals('admin.dashboard', $user->getDashboardRoute());
    }

    public function test_get_dashboard_route_returns_instructor_route_for_instructor(): void
    {
        $user = User::factory()->instructor()->create();

        $this->assertEquals('instructor.dashboard', $user->getDashboardRoute());
    }

    public function test_get_dashboard_route_returns_dashboard_route_for_student(): void
    {
        $user = User::factory()->student()->create();

        $this->assertEquals('dashboard', $user->getDashboardRoute());
    }
}


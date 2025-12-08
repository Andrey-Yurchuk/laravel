<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_form_can_be_displayed(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_user_can_login_with_remember_me(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
            'remember' => true,
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->from(route('login'))
            ->post(route('login'), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $response->assertRedirect(route('login'));
    }

    public function test_admin_redirects_to_admin_dashboard_after_login(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_instructor_redirects_to_instructor_dashboard_after_login(): void
    {
        $instructor = User::factory()->instructor()->create([
            'email' => 'instructor@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'instructor@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($instructor);
        $response->assertRedirect(route('instructor.dashboard'));
    }

    public function test_student_redirects_to_dashboard_after_login(): void
    {
        $student = User::factory()->student()->create([
            'email' => 'student@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'student@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($student);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_can_logout(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    public function test_login_requires_email(): void
    {
        $response = $this->from(route('login'))
            ->post(route('login'), [
                'password' => 'password123',
            ]);

        $response->assertSessionHasErrors('email');
        $response->assertRedirect(route('login'));
    }

    public function test_login_requires_password(): void
    {
        $response = $this->from(route('login'))
            ->post(route('login'), [
                'email' => 'test@example.com',
            ]);

        $response->assertSessionHasErrors('password');
        $response->assertRedirect(route('login'));
    }
}


<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_can_be_displayed(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.register');
    }

    public function test_user_can_register_as_student(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
            'terms' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_can_register_as_instructor(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test Instructor',
            'email' => 'instructor@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'instructor',
            'terms' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'instructor@example.com',
            'name' => 'Test Instructor',
        ]);

        $user = \App\Models\User::where('email', 'instructor@example.com')->first();
        $this->assertEquals(UserRole::Instructor, $user->role);

        $this->assertAuthenticated();
        $response->assertRedirect(route('instructor.dashboard'));
    }

    public function test_registration_requires_name(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'student',
                'terms' => true,
            ]);

        $response->assertSessionHasErrors('name');
        $response->assertRedirect(route('register'));
    }

    public function test_registration_requires_valid_email(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Test User',
                'email' => 'invalid-email',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'student',
                'terms' => true,
            ]);

        $response->assertSessionHasErrors('email');
        $response->assertRedirect(route('register'));
    }

    public function test_registration_requires_unique_email(): void
    {
        \App\Models\User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Test User',
                'email' => 'existing@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'student',
                'terms' => true,
            ]);

        $response->assertSessionHasErrors('email');
        $response->assertRedirect(route('register'));
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'different-password',
                'role' => 'student',
                'terms' => true,
            ]);

        $response->assertSessionHasErrors('password');
        $response->assertRedirect(route('register'));
    }

    public function test_registration_requires_minimum_password_length(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'short',
                'password_confirmation' => 'short',
                'role' => 'student',
                'terms' => true,
            ]);

        $response->assertSessionHasErrors('password');
        $response->assertRedirect(route('register'));
    }

    public function test_registration_requires_role(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'terms' => true,
            ]);

        $response->assertSessionHasErrors('role');
        $response->assertRedirect(route('register'));
    }

    public function test_registration_requires_valid_role(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'invalid-role',
                'terms' => true,
            ]);

        $response->assertSessionHasErrors('role');
        $response->assertRedirect(route('register'));
    }

    public function test_registration_requires_terms_acceptance(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'student',
                'terms' => false,
            ]);

        $response->assertSessionHasErrors('terms');
        $response->assertRedirect(route('register'));
    }
}


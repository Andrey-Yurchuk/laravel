<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_guest_cannot_access_categories_index(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_student_can_access_categories_index_but_cannot_create(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get(route('admin.categories.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_categories_index(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
    }

    public function test_admin_can_view_create_category_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.create');
    }

    public function test_admin_can_create_category(): void
    {
        $data = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), $data);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
    }

    public function test_admin_cannot_create_category_without_name(): void
    {
        $data = [
            'slug' => 'test-category',
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), $data);

        $response->assertSessionHasErrors('name');
        $response->assertRedirect(route('admin.categories.create'));
    }

    public function test_admin_cannot_create_category_with_duplicate_slug(): void
    {
        Category::factory()->create(['slug' => 'existing-slug']);

        $data = [
            'name' => 'Test Category',
            'slug' => 'existing-slug',
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), $data);

        $response->assertSessionHasErrors('slug');
        $response->assertRedirect(route('admin.categories.create'));
    }

    public function test_admin_can_view_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.show', $category->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.show');
    }

    public function test_admin_can_view_edit_category_form(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.edit', $category->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.edit');
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create([
            'name' => 'Old Name',
            'slug' => 'old-slug',
        ]);

        $data = [
            'name' => 'New Name',
            'slug' => 'new-slug',
            'description' => 'New description',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $category->id), $data);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'slug' => 'new-slug',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
    }

    public function test_admin_cannot_update_category_without_name(): void
    {
        $category = Category::factory()->create();

        $data = [
            'slug' => 'new-slug',
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.categories.edit', $category->id))
            ->put(route('admin.categories.update', $category->id), $data);

        $response->assertSessionHasErrors('name');
        $response->assertRedirect(route('admin.categories.edit', $category->id));
    }

    public function test_admin_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category->id));

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
    }

    public function test_non_admin_cannot_create_category(): void
    {
        $instructor = User::factory()->instructor()->create();

        $data = [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ];

        $response = $this->actingAs($instructor)
            ->post(route('admin.categories.store'), $data);

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_update_category(): void
    {
        $instructor = User::factory()->instructor()->create();
        $category = Category::factory()->create();

        $data = [
            'name' => 'New Name',
            'slug' => 'new-slug',
        ];

        $response = $this->actingAs($instructor)
            ->put(route('admin.categories.update', $category->id), $data);

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_delete_category(): void
    {
        $instructor = User::factory()->instructor()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($instructor)
            ->delete(route('admin.categories.destroy', $category->id));

        $response->assertForbidden();
        
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }
}


<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_be_created(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('test-category', $category->slug);
        $this->assertEquals('Test description', $category->description);
    }

    public function test_category_has_courses_relationship(): void
    {
        $category = Category::factory()->create();
        $relationship = $category->courses();

        $this->assertInstanceOf(HasMany::class, $relationship);
        $this->assertEquals(Course::class, $relationship->getRelated()::class);
    }
}


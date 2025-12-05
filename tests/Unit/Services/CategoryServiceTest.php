<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\DTOs\CategoryDTO;
use App\Models\Category;
use App\Services\CategoryService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    public function test_get_all_delegates_to_repository(): void
    {
        $repository = Mockery::mock(CategoryRepositoryInterface::class);
        $service = new CategoryService($repository);

        $collection = new Collection();

        $repository->shouldReceive('getAll')
            ->once()
            ->andReturn($collection);

        $result = $service->getAll();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_get_by_id_delegates_to_repository(): void
    {
        $repository = Mockery::mock(CategoryRepositoryInterface::class);
        $service = new CategoryService($repository);

        $category = new Category();

        $repository->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn($category);

        $result = $service->getById(1);

        $this->assertInstanceOf(Category::class, $result);
    }

    public function test_create_generates_slug_when_not_provided(): void
    {
        $repository = Mockery::mock(CategoryRepositoryInterface::class);
        $service = new CategoryService($repository);

        $dto = CategoryDTO::fromArray([
            'name' => 'Test Category Name',
            'slug' => '',
            'description' => 'Test description',
        ]);

        $category = new Category();
        $capturedData = null;

        $repository->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($data) use (&$capturedData, $category) {
                $capturedData = $data;
                return $category;
            });

        $service->create($dto);

        $this->assertNotNull($capturedData);
        $this->assertEquals('test-category-name', $capturedData['slug']);
    }

    public function test_create_does_not_generate_slug_when_provided(): void
    {
        $repository = Mockery::mock(CategoryRepositoryInterface::class);
        $service = new CategoryService($repository);

        $dto = CategoryDTO::fromArray([
            'name' => 'Test Category Name',
            'slug' => 'custom-slug',
            'description' => 'Test description',
        ]);

        $category = new Category();
        $capturedData = null;

        $repository->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($data) use (&$capturedData, $category) {
                $capturedData = $data;
                return $category;
            });

        $service->create($dto);

        $this->assertNotNull($capturedData);
        $this->assertEquals('custom-slug', $capturedData['slug']);
    }

    public function test_update_generates_slug_when_not_provided(): void
    {
        $repository = Mockery::mock(CategoryRepositoryInterface::class);
        $service = new CategoryService($repository);

        $dto = CategoryDTO::fromArray([
            'name' => 'Updated Category Name',
            'slug' => '',
            'description' => 'Updated description',
        ]);

        $category = new Category();
        $capturedData = null;

        $repository->shouldReceive('update')
            ->once()
            ->with(1, Mockery::any())
            ->andReturnUsing(function ($id, $data) use (&$capturedData, $category) {
                $capturedData = $data;
                return $category;
            });

        $service->update(1, $dto);

        $this->assertNotNull($capturedData);
        $this->assertEquals('updated-category-name', $capturedData['slug']);
    }

    public function test_delete_throws_exception_when_category_has_courses(): void
    {
        $repository = Mockery::mock(CategoryRepositoryInterface::class);
        $service = new CategoryService($repository);

        $repository->shouldReceive('hasCourses')
            ->once()
            ->with(1)
            ->andReturn(true);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Нельзя удалить категорию, в которой есть курсы');

        $service->delete(1);
    }

    public function test_delete_calls_repository_when_no_courses(): void
    {
        $repository = Mockery::mock(CategoryRepositoryInterface::class);
        $service = new CategoryService($repository);

        $repository->shouldReceive('hasCourses')
            ->once()
            ->with(1)
            ->andReturn(false);

        $repository->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $service->delete(1);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}


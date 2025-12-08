<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\DTOs\CategoryDTO;
use App\Models\Category;
use App\Services\CategoryService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    /** @var MockInterface&CategoryRepositoryInterface */
    private MockInterface $repository;
    private CategoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(CategoryRepositoryInterface::class);
        $this->service = new CategoryService($this->repository);
    }

    public function test_get_all_delegates_to_repository(): void
    {
        $collection = new Collection();

        $this->repository->shouldReceive('getAll')
            ->once()
            ->andReturn($collection);

        $result = $this->service->getAll();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_get_by_id_delegates_to_repository(): void
    {
        $category = new Category();

        $this->repository->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn($category);

        $result = $this->service->getById(1);

        $this->assertInstanceOf(Category::class, $result);
    }

    #[DataProvider('createSlugProvider')]
    public function test_create_handles_slug_generation(string $inputSlug, string $expectedSlug): void
    {
        $dto = CategoryDTO::fromArray([
            'name' => 'Test Category Name',
            'slug' => $inputSlug,
            'description' => 'Test description',
        ]);

        $category = new Category();
        $capturedData = null;

        $this->repository->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($data) use (&$capturedData, $category) {
                $capturedData = $data;
                return $category;
            });

        $this->service->create($dto);

        $this->assertNotNull($capturedData);
        $this->assertEquals($expectedSlug, $capturedData['slug']);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function createSlugProvider(): array
    {
        return [
            'slug generated when empty' => ['', 'test-category-name'],
            'slug kept when provided' => ['custom-slug', 'custom-slug'],
        ];
    }

    public function test_update_generates_slug_when_not_provided(): void
    {
        $dto = CategoryDTO::fromArray([
            'name' => 'Updated Category Name',
            'slug' => '',
            'description' => 'Updated description',
        ]);

        $category = new Category();
        $capturedData = null;

        $this->repository->shouldReceive('update')
            ->once()
            ->with(1, Mockery::any())
            ->andReturnUsing(function ($id, $data) use (&$capturedData, $category) {
                $capturedData = $data;
                return $category;
            });

        $this->service->update(1, $dto);

        $this->assertNotNull($capturedData);
        $this->assertEquals('updated-category-name', $capturedData['slug']);
    }

    public function test_delete_throws_exception_when_category_has_courses(): void
    {
        $this->repository->shouldReceive('hasCourses')
            ->once()
            ->with(1)
            ->andReturn(true);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Нельзя удалить категорию, в которой есть курсы');

        $this->service->delete(1);
    }

    public function test_delete_calls_repository_when_no_courses(): void
    {
        $this->repository->shouldReceive('hasCourses')
            ->once()
            ->with(1)
            ->andReturn(false);

        $this->repository->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $this->service->delete(1);

        $this->assertTrue(true);
    }
}


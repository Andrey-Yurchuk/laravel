<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\CourseRepositoryInterface;
use App\Contracts\Services\CacheServiceInterface;
use App\DTOs\CourseDTO;
use App\Models\Course;
use App\Services\CourseService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CourseServiceTest extends TestCase
{
    /** @var MockInterface&CourseRepositoryInterface */
    private MockInterface $repository;
    /** @var MockInterface&CacheServiceInterface */
    private MockInterface $cacheService;
    private CourseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(CourseRepositoryInterface::class);
        $this->cacheService = Mockery::mock(CacheServiceInterface::class);
        $this->service = new CourseService($this->repository, $this->cacheService);
    }

    public function test_get_all_delegates_to_repository(): void
    {
        $paginator = new Paginator([], 0, 15);

        $this->repository->shouldReceive('getAll')
            ->once()
            ->with(15)
            ->andReturn($paginator);

        $result = $this->service->getAll();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_get_by_id_uses_cache_service(): void
    {
        $course = new Course();

        $this->cacheService->shouldReceive('rememberCourse')
            ->once()
            ->andReturnUsing(function ($id, $callback) use ($course) {
                $this->repository->shouldReceive('getById')
                    ->once()
                    ->with($id)
                    ->andReturn($course);
                return $callback();
            });

        $result = $this->service->getById(1);

        $this->assertInstanceOf(Course::class, $result);
        $this->assertSame($course, $result);
    }

    #[DataProvider('createSlugProvider')]
    public function test_create_handles_slug_generation(string $inputSlug, string $expectedSlug): void
    {
        $dto = CourseDTO::fromArray([
            'instructor_id' => 1,
            'category_id' => 1,
            'title' => 'Test Course Title',
            'slug' => $inputSlug,
            'description' => 'Test description',
            'difficulty_level' => 'beginner',
            'status' => 'draft',
        ]);

        $course = new Course();
        $capturedData = null;

        $course->id = 1;
        $course->instructor_id = 1;

        $this->repository->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($data) use (&$capturedData, $course) {
                $capturedData = $data;
                return $course;
            });

        $this->cacheService->shouldReceive('forgetCourseCache')
            ->once()
            ->with(1, 1);

        $this->service->create($dto);

        $this->assertNotNull($capturedData);
        $this->assertEquals($expectedSlug, $capturedData['slug']);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function createSlugProvider(): array
    {
        return [
            'slug generated when empty' => ['', 'test-course-title'],
            'slug kept when provided' => ['custom-slug', 'custom-slug'],
        ];
    }

    public function test_update_generates_slug_when_not_provided(): void
    {
        $dto = CourseDTO::fromArray([
            'instructor_id' => 1,
            'category_id' => 1,
            'title' => 'Updated Course Title',
            'slug' => '',
            'description' => 'Updated description',
            'difficulty_level' => 'intermediate',
            'status' => 'published',
        ]);

        $course = new Course();
        $capturedData = null;

        $course->instructor_id = 1;

        $this->repository->shouldReceive('update')
            ->once()
            ->with(1, Mockery::any())
            ->andReturnUsing(function ($id, $data) use (&$capturedData, $course) {
                $capturedData = $data;
                return $course;
            });

        $this->cacheService->shouldReceive('forgetCourseCache')
            ->once()
            ->with(1, 1);

        $this->service->update(1, $dto);

        $this->assertNotNull($capturedData);
        $this->assertEquals('updated-course-title', $capturedData['slug']);
    }

    public function test_delete_throws_exception_when_course_has_subscriptions(): void
    {
        $this->repository->shouldReceive('hasSubscriptions')
            ->once()
            ->with(1)
            ->andReturn(true);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Нельзя удалить курс, на который есть подписки');

        $this->service->delete(1);
    }

    public function test_delete_calls_repository_when_no_subscriptions(): void
    {
        $course = new Course();
        $course->id = 1;
        $course->instructor_id = 1;

        $this->repository->shouldReceive('hasSubscriptions')
            ->once()
            ->with(1)
            ->andReturn(false);

        $this->repository->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn($course);

        $this->repository->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $this->cacheService->shouldReceive('forgetCourseCache')
            ->once()
            ->with(1, 1);

        $this->service->delete(1);

        $this->assertTrue(true);
    }

    public function test_get_by_instructor_id_delegates_to_repository(): void
    {
        $paginator = new Paginator([], 0, 15);

        $this->repository->shouldReceive('getByInstructorId')
            ->once()
            ->with(1, 15)
            ->andReturn($paginator);

        $result = $this->service->getByInstructorId(1);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_count_published_by_instructor_id_uses_cache_service(): void
    {
        $this->cacheService->shouldReceive('rememberCoursesCountPublishedByInstructor')
            ->once()
            ->andReturnUsing(function ($instructorId, $callback) {
                $this->repository->shouldReceive('countPublishedByInstructorId')
                    ->once()
                    ->with($instructorId)
                    ->andReturn(5);
                return $callback();
            });

        $result = $this->service->countPublishedByInstructorId(1);

        $this->assertEquals(5, $result);

        $this->assertEquals(5, $result);
    }
}


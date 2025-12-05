<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\CourseRepositoryInterface;
use App\DTOs\CourseDTO;
use App\Models\Course;
use App\Services\CourseService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Mockery;
use Tests\TestCase;

class CourseServiceTest extends TestCase
{
    public function test_get_all_delegates_to_repository(): void
    {
        $repository = Mockery::mock(CourseRepositoryInterface::class);
        $service = new CourseService($repository);

        $paginator = new Paginator([], 0, 15);

        $repository->shouldReceive('getAll')
            ->once()
            ->with(15)
            ->andReturn($paginator);

        $result = $service->getAll();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_get_by_id_delegates_to_repository(): void
    {
        $repository = Mockery::mock(CourseRepositoryInterface::class);
        $service = new CourseService($repository);

        $course = new Course();

        $repository->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn($course);

        $result = $service->getById(1);

        $this->assertInstanceOf(Course::class, $result);
    }

    public function test_create_generates_slug_when_not_provided(): void
    {
        $repository = Mockery::mock(CourseRepositoryInterface::class);
        $service = new CourseService($repository);

        $dto = CourseDTO::fromArray([
            'instructor_id' => 1,
            'category_id' => 1,
            'title' => 'Test Course Title',
            'slug' => '',
            'description' => 'Test description',
            'difficulty_level' => 'beginner',
            'status' => 'draft',
        ]);

        $course = new Course();
        $capturedData = null;

        $repository->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($data) use (&$capturedData, $course) {
                $capturedData = $data;
                return $course;
            });

        $service->create($dto);

        $this->assertNotNull($capturedData);
        $this->assertEquals('test-course-title', $capturedData['slug']);
    }

    public function test_create_does_not_generate_slug_when_provided(): void
    {
        $repository = Mockery::mock(CourseRepositoryInterface::class);
        $service = new CourseService($repository);

        $dto = CourseDTO::fromArray([
            'instructor_id' => 1,
            'category_id' => 1,
            'title' => 'Test Course Title',
            'slug' => 'custom-slug',
            'description' => 'Test description',
            'difficulty_level' => 'beginner',
            'status' => 'draft',
        ]);

        $course = new Course();
        $capturedData = null;

        $repository->shouldReceive('create')
            ->once()
            ->andReturnUsing(function ($data) use (&$capturedData, $course) {
                $capturedData = $data;
                return $course;
            });

        $service->create($dto);

        $this->assertNotNull($capturedData);
        $this->assertEquals('custom-slug', $capturedData['slug']);
    }

    public function test_update_generates_slug_when_not_provided(): void
    {
        $repository = Mockery::mock(CourseRepositoryInterface::class);
        $service = new CourseService($repository);

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

        $repository->shouldReceive('update')
            ->once()
            ->with(1, Mockery::any())
            ->andReturnUsing(function ($id, $data) use (&$capturedData, $course) {
                $capturedData = $data;
                return $course;
            });

        $service->update(1, $dto);

        $this->assertNotNull($capturedData);
        $this->assertEquals('updated-course-title', $capturedData['slug']);
    }

    public function test_delete_throws_exception_when_course_has_subscriptions(): void
    {
        $repository = Mockery::mock(CourseRepositoryInterface::class);
        $service = new CourseService($repository);

        $repository->shouldReceive('hasSubscriptions')
            ->once()
            ->with(1)
            ->andReturn(true);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Нельзя удалить курс, на который есть подписки');

        $service->delete(1);
    }

    public function test_delete_calls_repository_when_no_subscriptions(): void
    {
        $repository = Mockery::mock(CourseRepositoryInterface::class);
        $service = new CourseService($repository);

        $repository->shouldReceive('hasSubscriptions')
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

    public function test_get_by_instructor_id_delegates_to_repository(): void
    {
        $repository = Mockery::mock(CourseRepositoryInterface::class);
        $service = new CourseService($repository);

        $paginator = new Paginator([], 0, 15);

        $repository->shouldReceive('getByInstructorId')
            ->once()
            ->with(1, 15)
            ->andReturn($paginator);

        $result = $service->getByInstructorId(1);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_count_published_by_instructor_id_delegates_to_repository(): void
    {
        $repository = Mockery::mock(CourseRepositoryInterface::class);
        $service = new CourseService($repository);

        $repository->shouldReceive('countPublishedByInstructorId')
            ->once()
            ->with(1)
            ->andReturn(5);

        $result = $service->countPublishedByInstructorId(1);

        $this->assertEquals(5, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}


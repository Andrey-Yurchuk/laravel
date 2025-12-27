<?php

namespace App\Services;

use App\Contracts\Repositories\CourseRepositoryInterface;
use App\Contracts\Services\CacheServiceInterface;
use App\Contracts\Services\CourseServiceInterface;
use App\DTOs\CourseDTO;
use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Exception;

class CourseService implements CourseServiceInterface
{
    public function __construct(
        private CourseRepositoryInterface $repository,
        private CacheServiceInterface $cacheService
    ) {
    }

    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($perPage);
    }

    public function getById(int $id): Course
    {
        return $this->cacheService->rememberCourse($id, function () use ($id) {
            return $this->repository->getById($id);
        });
    }

    public function create(CourseDTO $dto): Course
    {
        $data = $dto->toArray();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $course = $this->repository->create($data);

        $this->cacheService->forgetCourseCache($course->id, $course->instructor_id);

        return $course;
    }

    public function update(int $id, CourseDTO $dto): Course
    {
        $data = $dto->toArray();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $course = $this->repository->update($id, $data);

        $this->cacheService->forgetCourseCache($id, $course->instructor_id);

        return $course;
    }

    public function delete(int $id): void
    {
        if ($this->repository->hasSubscriptions($id)) {
            throw new Exception('Нельзя удалить курс, на который есть подписки');
        }

        $course = $this->repository->getById($id);
        $instructorId = $course->instructor_id;

        $this->repository->delete($id);

        $this->cacheService->forgetCourseCache($id, $instructorId);
    }

    public function count(): int
    {
        return $this->repository->count();
    }

    public function countPublished(): int
    {
        return $this->cacheService->rememberCoursesCountPublished(function () {
            return $this->repository->countPublished();
        });
    }

    public function getRecent(int $limit = 5): Collection
    {
        return $this->cacheService->rememberCoursesRecent($limit, function () use ($limit) {
            return $this->repository->getRecent($limit);
        });
    }

    public function getInstructors(): Collection
    {
        return $this->repository->getInstructors();
    }

    public function getByInstructorId(int $instructorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByInstructorId($instructorId, $perPage);
    }

    public function countPublishedByInstructorId(int $instructorId): int
    {
        return $this->cacheService->rememberCoursesCountPublishedByInstructor(
            $instructorId,
            function () use ($instructorId) {
                return $this->repository->countPublishedByInstructorId($instructorId);
            }
        );
    }
}

<?php

namespace App\Repositories;

use App\Contracts\Repositories\CourseRepositoryInterface;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository implements CourseRepositoryInterface
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Course::query();
        return $query->with(['category', 'instructor'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getById(int $id): Course
    {
        /** @var \Illuminate\Database\Eloquent\Builder<Course> $query */
        $query = Course::query();
        /** @var Course $result */
        $result = $query->with(['category', 'instructor', 'lessons'])
            ->findOrFail($id);
        return $result;
    }

    public function create(array $data): Course
    {
        /** @var \Illuminate\Database\Eloquent\Builder<Course> $query */
        $query = Course::query();
        /** @var Course $result */
        $result = $query->create($data);
        return $result;
    }

    public function update(int $id, array $data): Course
    {
        $course = $this->getById($id);
        $course->update($data);
        return $course->fresh(['category', 'instructor']);
    }

    public function delete(int $id): bool
    {
        $course = $this->getById($id);
        return $course->delete();
    }

    public function hasSubscriptions(int $id): bool
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Course::query();
        return $query->where('id', $id)
            ->whereHas('subscriptions')
            ->exists();
    }

    public function count(): int
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Course::query();
        return $query->count();
    }

    public function countPublished(): int
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Course::query();
        return $query->where('status', CourseStatus::Published)->count();
    }

    public function getRecent(int $limit = 5): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Course::query();
        /** @var Collection $result */
        $result = $query->with(['category', 'instructor'])
            ->latest()
            ->limit($limit)
            ->get();
        return $result;
    }

    public function getInstructors(): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = User::query();
        return $query->where('role', UserRole::Instructor)->get();
    }

    public function getByInstructorId(int $instructorId, int $perPage = 15): LengthAwarePaginator
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Course::query();
        return $query->with(['category', 'instructor'])
            ->where('instructor_id', $instructorId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function countPublishedByInstructorId(int $instructorId): int
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Course::query();
        return $query->where('instructor_id', $instructorId)
            ->where('status', CourseStatus::Published)
            ->count();
    }
}

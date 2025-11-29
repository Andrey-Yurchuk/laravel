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
        return Course::with(['category', 'instructor'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getById(int $id): Course
    {
        return Course::with(['category', 'instructor', 'lessons'])
            ->findOrFail($id);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
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
        return Course::where('id', $id)
            ->whereHas('subscriptions')
            ->exists();
    }

    public function count(): int
    {
        return Course::count();
    }

    public function countPublished(): int
    {
        return Course::where('status', CourseStatus::Published)->count();
    }

    public function getRecent(int $limit = 5): Collection
    {
        return Course::with(['category', 'instructor'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getInstructors(): Collection
    {
        return User::where('role', UserRole::Instructor)->get();
    }
}


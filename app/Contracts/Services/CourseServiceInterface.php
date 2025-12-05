<?php

namespace App\Contracts\Services;

use App\DTOs\CourseDTO;
use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CourseServiceInterface
{
    public function getAll(int $perPage = 15): LengthAwarePaginator;

    public function getById(int $id): Course;

    public function create(CourseDTO $dto): Course;

    public function update(int $id, CourseDTO $dto): Course;

    public function delete(int $id): void;

    public function count(): int;

    public function countPublished(): int;

    public function getRecent(int $limit = 5): Collection;

    public function getInstructors(): Collection;

    public function getByInstructorId(int $instructorId, int $perPage = 15): LengthAwarePaginator;

    public function countPublishedByInstructorId(int $instructorId): int;
}

<?php

namespace App\Services;

use App\Contracts\Repositories\CourseRepositoryInterface;
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
        private CourseRepositoryInterface $repository
    ) {
    }

    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($perPage);
    }

    public function getById(int $id): Course
    {
        return $this->repository->getById($id);
    }

    public function create(CourseDTO $dto): Course
    {
        $data = $dto->toArray();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $this->repository->create($data);
    }

    public function update(int $id, CourseDTO $dto): Course
    {
        $data = $dto->toArray();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): void
    {
        if ($this->repository->hasSubscriptions($id)) {
            throw new Exception('Нельзя удалить курс, на который есть подписки');
        }

        $this->repository->delete($id);
    }

    public function count(): int
    {
        return $this->repository->count();
    }

    public function countPublished(): int
    {
        return $this->repository->countPublished();
    }

    public function getRecent(int $limit = 5): Collection
    {
        return $this->repository->getRecent($limit);
    }

    public function getInstructors(): Collection
    {
        return $this->repository->getInstructors();
    }
}

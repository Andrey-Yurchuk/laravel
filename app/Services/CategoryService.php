<?php

namespace App\Services;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Services\CategoryServiceInterface;
use App\DTOs\CategoryDTO;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Exception;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private CategoryRepositoryInterface $repository
    ) {
    }

    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }

    public function getById(int $id): Category
    {
        return $this->repository->getById($id);
    }

    public function create(CategoryDTO $dto): Category
    {
        $data = $dto->toArray();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->repository->create($data);
    }

    public function update(int $id, CategoryDTO $dto): Category
    {
        $data = $dto->toArray();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): void
    {
        if ($this->repository->hasCourses($id)) {
            throw new Exception('Нельзя удалить категорию, в которой есть курсы');
        }

        $this->repository->delete($id);
    }

    public function count(): int
    {
        return $this->repository->count();
    }
}

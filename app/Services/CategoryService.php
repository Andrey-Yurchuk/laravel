<?php

namespace App\Services;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Services\CacheServiceInterface;
use App\Contracts\Services\CategoryServiceInterface;
use App\DTOs\CategoryDTO;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Exception;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private CategoryRepositoryInterface $repository,
        private CacheServiceInterface $cacheService
    ) {
    }

    public function getAll(): Collection
    {
        return $this->cacheService->rememberCategoriesAll(function () {
            return $this->repository->getAll();
        });
    }

    public function getById(int $id): Category
    {
        return $this->cacheService->rememberCategory($id, function () use ($id) {
            return $this->repository->getById($id);
        });
    }

    public function create(CategoryDTO $dto): Category
    {
        $data = $dto->toArray();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category = $this->repository->create($data);

        $this->cacheService->forgetCategoryCache($category->id);

        return $category;
    }

    public function update(int $id, CategoryDTO $dto): Category
    {
        $data = $dto->toArray();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category = $this->repository->update($id, $data);

        $this->cacheService->forgetCategoryCache($id);

        return $category;
    }

    public function delete(int $id): void
    {
        if ($this->repository->hasCourses($id)) {
            throw new Exception('Нельзя удалить категорию, в которой есть курсы');
        }

        $this->repository->delete($id);

        $this->cacheService->forgetCategoryCache($id);
    }

    public function count(): int
    {
        return $this->cacheService->rememberCategoriesCount(function () {
            return $this->repository->count();
        });
    }
}

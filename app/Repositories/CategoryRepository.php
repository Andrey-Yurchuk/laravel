<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Builder<Category> $query */
        $query = Category::query();
        /** @var Collection<int, Category> $result */
        $result = $query->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public function getById(int $id): Category
    {
        /** @var \Illuminate\Database\Eloquent\Builder<Category> $query */
        $query = Category::query();
        /** @var Category $result */
        $result = $query->findOrFail($id);
        return $result;
    }

    public function create(array $data): Category
    {
        /** @var \Illuminate\Database\Eloquent\Builder<Category> $query */
        $query = Category::query();
        /** @var Category $result */
        $result = $query->create($data);
        return $result;
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->getById($id);
        $category->update($data);
        return $category->fresh();
    }

    public function delete(int $id): bool
    {
        $category = $this->getById($id);
        return $category->delete();
    }

    public function hasCourses(int $id): bool
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Category::query();
        return $query->where('id', $id)
            ->whereHas('courses')
            ->exists();
    }

    public function count(): int
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Category::query();
        return $query->count();
    }
}

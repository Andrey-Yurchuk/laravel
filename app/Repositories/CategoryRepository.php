<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(): Collection
    {
        return Category::orderBy('created_at', 'desc')->get();
    }

    public function getById(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
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
        return Category::where('id', $id)
            ->whereHas('courses')
            ->exists();
    }

    public function count(): int
    {
        return Category::count();
    }
}


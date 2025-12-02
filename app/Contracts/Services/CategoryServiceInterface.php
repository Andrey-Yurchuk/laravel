<?php

namespace App\Contracts\Services;

use App\DTOs\CategoryDTO;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{
    public function getAll(): Collection;

    public function getById(int $id): Category;

    public function create(CategoryDTO $dto): Category;

    public function update(int $id, CategoryDTO $dto): Category;

    public function delete(int $id): void;

    public function count(): int;
}

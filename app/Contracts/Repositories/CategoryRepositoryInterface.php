<?php

namespace App\Contracts\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function getAll(): Collection;

    public function getById(int $id): Category;

    public function create(array $data): Category;

    public function update(int $id, array $data): Category;

    public function delete(int $id): bool;

    public function hasCourses(int $id): bool;

    public function count(): int;
}


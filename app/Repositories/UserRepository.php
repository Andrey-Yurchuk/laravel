<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        /** @var \Illuminate\Database\Eloquent\Builder<User> $query */
        $query = User::query();
        /** @var User $result */
        $result = $query->create($data);
        return $result;
    }
}

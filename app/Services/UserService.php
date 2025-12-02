<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {
    }

    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = UserRole::from($data['role']);

        return $this->repository->create($data);
    }
}

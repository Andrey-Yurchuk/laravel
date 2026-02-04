<?php

namespace App\Contracts\Services;

use App\Models\User;

interface UserServiceInterface
{
    public function register(array $data): User;

    public function authenticateForApi(string $email, string $password): User;
}

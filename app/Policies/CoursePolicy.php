<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin 
            || $user->role === UserRole::Instructor;
    }

    public function view(User $user, Course $course): bool
    {
        return $user->role === UserRole::Admin 
            || ($user->role === UserRole::Instructor && $course->instructor_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin 
            || $user->role === UserRole::Instructor;
    }

    public function update(User $user, Course $course): bool
    {
        return $user->role === UserRole::Admin 
            || ($user->role === UserRole::Instructor && $course->instructor_id === $user->id);
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function restore(User $user, Course $course): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function forceDelete(User $user, Course $course): bool
    {
        return $user->role === UserRole::Admin;
    }
}

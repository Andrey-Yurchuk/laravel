<?php

namespace App\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Instructor = 'instructor';
    case Admin = 'admin';

    public function getDashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Instructor => 'instructor.dashboard',
            default => 'dashboard',
        };
    }
}

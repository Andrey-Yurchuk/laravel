<?php

namespace App\Http\Requests\Admin;

use App\Enums\CourseDifficulty;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()?->role === UserRole::Admin;
    }

    public function rules(): array
    {
        return [
            'instructor_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug',
            'description' => 'required|string',
            'difficulty_level' => 'required|in:' . implode(',', array_column(CourseDifficulty::cases(), 'value')),
            'status' => 'required|in:' . implode(',', array_column(CourseStatus::cases(), 'value')),
        ];
    }
}

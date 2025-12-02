<?php

namespace App\Http\Requests\Instructor;

use App\Enums\CourseDifficulty;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreInstructorCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || $user->role !== UserRole::Instructor) {
            return false;
        }

        return $this->user()->can('create', Course::class);
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug',
            'description' => 'required|string',
            'difficulty_level' => 'required|in:' . implode(',', array_column(CourseDifficulty::cases(), 'value')),
            'status' => 'required|in:' . implode(',', array_column(CourseStatus::cases(), 'value')),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'instructor_id' => Auth::id(),
        ]);
    }
}

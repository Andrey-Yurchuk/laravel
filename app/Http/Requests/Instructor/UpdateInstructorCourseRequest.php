<?php

namespace App\Http\Requests\Instructor;

use App\Enums\CourseDifficulty;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateInstructorCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || $user->role !== UserRole::Instructor) {
            return false;
        }

        $courseId = $this->route('course');
        /** @var \Illuminate\Database\Eloquent\Builder<Course> $query */
        $query = Course::query();
        /** @var Course $course */
        $course = $query->findOrFail($courseId);

        return $this->user()->can('update', $course);
    }

    public function rules(): array
    {
        $courseId = $this->route('course');

        return [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('courses', 'slug')->ignore($courseId),
            ],
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

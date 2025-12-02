<?php

namespace App\Http\Controllers\Instructor;

use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\CourseServiceInterface;
use App\DTOs\CourseDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreInstructorCourseRequest;
use App\Http\Requests\Instructor\UpdateInstructorCourseRequest;
use App\Models\Course;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class InstructorCourseController extends Controller
{
    public function __construct(
        private CourseServiceInterface $courseService,
        private CategoryServiceInterface $categoryService
    ) {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Course::class);

        $courses = $this->courseService->getByInstructorId(Auth::id());

        return view('instructor.courses.index', compact('courses'));
    }

    public function create(): View
    {
        $this->authorize('create', Course::class);

        $categories = $this->categoryService->getAll();

        return view('instructor.courses.create', compact('categories'));
    }

    public function store(StoreInstructorCourseRequest $request): RedirectResponse
    {
        $this->authorize('create', Course::class);

        $validated = $request->validated();
        $validated['instructor_id'] = Auth::id();

        $dto = CourseDTO::fromArray($validated);

        try {
            $this->courseService->create($dto);
            return redirect()->route('instructor.courses.index')
                ->with('success', 'Курс успешно создан');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $course = $this->courseService->getById($id);

        $this->authorize('view', $course);

        return view('instructor.courses.show', compact('course'));
    }

    public function edit(int $id): View
    {
        $course = $this->courseService->getById($id);

        $this->authorize('update', $course);

        $categories = $this->categoryService->getAll();

        return view('instructor.courses.edit', compact('course', 'categories'));
    }

    public function update(UpdateInstructorCourseRequest $request, int $id): RedirectResponse
    {
        $course = $this->courseService->getById($id);

        $this->authorize('update', $course);

        $validated = $request->validated();
        $validated['instructor_id'] = Auth::id();

        $dto = CourseDTO::fromArray($validated);

        try {
            $this->courseService->update($id, $dto);
            return redirect()->route('instructor.courses.index')
                ->with('success', 'Курс успешно обновлен');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $course = $this->courseService->getById($id);

        $this->authorize('delete', $course);

        try {
            $this->courseService->delete($id);
            return redirect()->route('instructor.courses.index')
                ->with('success', 'Курс успешно удален');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}

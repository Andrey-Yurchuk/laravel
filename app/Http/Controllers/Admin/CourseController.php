<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\CourseServiceInterface;
use App\DTOs\CourseDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CourseController extends Controller
{
    public function __construct(
        private CourseServiceInterface $courseService,
        private CategoryServiceInterface $categoryService
    ) {
    }

    public function index(): View
    {
        $courses = $this->courseService->getAll();

        return view('admin.courses.index', compact('courses'));
    }

    public function create(): View
    {
        $categories = $this->categoryService->getAll();
        $instructors = $this->courseService->getInstructors();

        return view('admin.courses.create', compact('categories', 'instructors'));
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $dto = CourseDTO::fromArray($request->validated());

        try {
            $this->courseService->create($dto);
            return redirect()->route('admin.courses.index')
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

        return view('admin.courses.show', compact('course'));
    }

    public function edit(int $id): View
    {
        $course = $this->courseService->getById($id);
        $categories = $this->categoryService->getAll();
        $instructors = $this->courseService->getInstructors();

        return view('admin.courses.edit', compact('course', 'categories', 'instructors'));
    }

    public function update(UpdateCourseRequest $request, int $id): RedirectResponse
    {
        $dto = CourseDTO::fromArray($request->validated());

        try {
            $this->courseService->update($id, $dto);
            return redirect()->route('admin.courses.index')
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
            return redirect()->route('admin.courses.index')
                ->with('success', 'Курс успешно удален');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}

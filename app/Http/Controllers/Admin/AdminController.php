<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\CourseServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class AdminController extends Controller
{
    public function __construct(
        private CategoryServiceInterface $categoryService,
        private CourseServiceInterface $courseService
    ) {
    }

    public function index(): View
    {
        $stats = [
            'categories_count' => $this->categoryService->count(),
            'courses_count' => $this->courseService->count(),
            'published_courses' => $this->courseService->countPublished(),
        ];

        $recentCourses = $this->courseService->getRecent(5);

        return view('admin.dashboard', compact('stats', 'recentCourses'));
    }
}

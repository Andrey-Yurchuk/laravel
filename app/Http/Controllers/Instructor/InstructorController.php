<?php

namespace App\Http\Controllers\Instructor;

use App\Contracts\Services\CourseServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class InstructorController extends Controller
{
    public function __construct(
        private CourseServiceInterface $courseService
    ) {
    }

    public function index(): View
    {
        $instructorId = Auth::id();
        $courses = $this->courseService->getByInstructorId($instructorId, 5);

        $stats = [
            'total_courses' => $courses->total(),
            'published_courses' => $this->courseService->countPublishedByInstructorId($instructorId),
        ];

        return view('instructor.dashboard', compact('stats', 'courses'));
    }
}

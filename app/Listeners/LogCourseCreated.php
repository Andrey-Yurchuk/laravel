<?php

namespace App\Listeners;

use App\Events\CourseCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogCourseCreated implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CourseCreated $event): void
    {
        $course = $event->course;

        Log::channel('single')->info('Course created event processed', [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'instructor_id' => $course->instructor_id,
            'instructor_name' => $course->instructor->name,
            'category_id' => $course->category_id,
            'category_name' => $course->category->name,
            'difficulty_level' => $course->difficulty_level->value,
            'status' => $course->status->value,
            'created_at' => $course->created_at->toDateTimeString(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}

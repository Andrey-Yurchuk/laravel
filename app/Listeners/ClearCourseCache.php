<?php

namespace App\Listeners;

use App\Contracts\Services\CacheServiceInterface;
use App\Events\CourseCreated;
use App\Events\CourseDeleted;
use App\Events\CourseUpdated;

class ClearCourseCache
{
    public function __construct(
        private CacheServiceInterface $cacheService
    ) {
    }

    public function handle(CourseCreated|CourseUpdated|CourseDeleted $event): void
    {
        $course = $event->course;
        $this->cacheService->forgetCourseCache($course->id, $course->instructor_id);
    }
}

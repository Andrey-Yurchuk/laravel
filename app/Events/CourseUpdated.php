<?php

namespace App\Events;

use App\Models\Course;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Course $course
    ) {
    }
}

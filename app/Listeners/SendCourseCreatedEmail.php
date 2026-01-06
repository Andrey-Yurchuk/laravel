<?php

namespace App\Listeners;

use App\Events\CourseCreated;
use App\Mail\CourseCreatedNotification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCourseCreatedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CourseCreated $event): void
    {
        $course = $event->course;
        $instructor = $course->instructor;

        try {
            Mail::to($instructor->email)
                ->send(new CourseCreatedNotification($course));

            Log::info('Course created email sent', [
                'course_id' => $course->id,
                'instructor_email' => $instructor->email,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send course created email', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}

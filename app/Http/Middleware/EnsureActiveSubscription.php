<?php

namespace App\Http\Middleware;

use App\Enums\SubscriptionStatus;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subscription;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        if ($this->isPreviewLesson($request)) {
            return $next($request);
        }

        $courseId = $this->resolveCourseId($request);
        if (! $courseId) {
            abort(Response::HTTP_BAD_REQUEST, 'Не удалось определить курс для проверки подписки.');
        }

        $hasActiveSubscription = Subscription::query()
            ->where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', SubscriptionStatus::Active)
            ->where(function ($query) {
                $query
                    ->whereNull('current_period_end')
                    ->orWhere('current_period_end', '>=', now());
            })
            ->exists();

        if (! $hasActiveSubscription) {
            abort(Response::HTTP_FORBIDDEN, 'Требуется активная подписка для доступа к материалам курса.');
        }

        return $next($request);
    }

    private function resolveCourseId(Request $request): ?int
    {
        $route = $request->route();
        $course = $route->parameter('course');
        if ($course instanceof Course) {
            return $course->id;
        }
        if (is_numeric($course)) {
            return (int) $course;
        }

        $lesson = $route->parameter('lesson');
        if ($lesson instanceof Lesson) {
            return $lesson->course_id;
        }
        if (is_numeric($lesson)) {
            $lessonModel = Lesson::query()->find($lesson);
            return $lessonModel?->course_id;
        }

        return null;
    }

    private function isPreviewLesson(Request $request): bool
    {
        $route = $request->route();
        $lesson = $route->parameter('lesson');
        if ($lesson instanceof Lesson) {
            return (bool) $lesson->is_preview;
        }
        if (is_numeric($lesson)) {
            $lessonModel = Lesson::query()->find($lesson);
            return (bool) ($lessonModel?->is_preview);
        }

        return false;
    }
}

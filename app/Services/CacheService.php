<?php

namespace App\Services;

use App\Contracts\Services\CacheServiceInterface;
use Illuminate\Support\Facades\Cache;

class CacheService implements CacheServiceInterface
{
    private const TTL_CATEGORIES = 3600;
    private const TTL_CATEGORIES_COUNT = 1800;
    private const TTL_COURSES = 1800;
    private const TTL_COURSES_RECENT = 900;
    private const TTL_COURSES_STATISTICS = 1800;

    public function rememberCategoriesAll(callable $callback, ?int $ttl = null)
    {
        return Cache::remember('categories:all', $ttl ?? self::TTL_CATEGORIES, $callback);
    }

    public function rememberCategory(int $id, callable $callback, ?int $ttl = null)
    {
        return Cache::remember("category:{$id}", $ttl ?? self::TTL_CATEGORIES, $callback);
    }

    public function rememberCategoriesCount(callable $callback, ?int $ttl = null)
    {
        return Cache::remember('categories:count', $ttl ?? self::TTL_CATEGORIES_COUNT, $callback);
    }

    public function rememberCourse(int $id, callable $callback, ?int $ttl = null)
    {
        return Cache::remember("course:{$id}", $ttl ?? self::TTL_COURSES, $callback);
    }

    public function rememberCoursesRecent(int $limit, callable $callback, ?int $ttl = null)
    {
        return Cache::remember("courses:recent:{$limit}", $ttl ?? self::TTL_COURSES_RECENT, $callback);
    }

    public function rememberCoursesCountPublished(callable $callback, ?int $ttl = null)
    {
        return Cache::remember('courses:count:published', $ttl ?? self::TTL_COURSES_STATISTICS, $callback);
    }

    public function rememberCoursesCountPublishedByInstructor(int $instructorId, callable $callback, ?int $ttl = null)
    {
        $key = "courses:count:published:instructor:{$instructorId}";
        return Cache::remember($key, $ttl ?? self::TTL_COURSES_STATISTICS, $callback);
    }

    public function forgetCategoryCache(?int $categoryId = null): void
    {
        Cache::forget('categories:all');
        Cache::forget('categories:count');

        if ($categoryId !== null) {
            Cache::forget("category:{$categoryId}");
        }
    }

    public function forgetCourseCache(?int $courseId = null, ?int $instructorId = null): void
    {
        if ($courseId !== null) {
            Cache::forget("course:{$courseId}");
        }

        Cache::forget('courses:count:published');

        if ($instructorId !== null) {
            Cache::forget("courses:count:published:instructor:{$instructorId}");
        }

        for ($limit = 5; $limit <= 20; $limit += 5) {
            Cache::forget("courses:recent:{$limit}");
        }
    }
}

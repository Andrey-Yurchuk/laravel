<?php

namespace App\Contracts\Services;

interface CacheServiceInterface
{
    public function rememberCategoriesAll(callable $callback, ?int $ttl = null);

    public function rememberCategory(int $id, callable $callback, ?int $ttl = null);

    public function rememberCategoriesCount(callable $callback, ?int $ttl = null);

    public function rememberCourse(int $id, callable $callback, ?int $ttl = null);

    public function rememberCoursesRecent(int $limit, callable $callback, ?int $ttl = null);

    public function rememberCoursesCountPublished(callable $callback, ?int $ttl = null);

    public function rememberCoursesCountPublishedByInstructor(int $instructorId, callable $callback, ?int $ttl = null);

    public function forgetCategoryCache(?int $categoryId = null): void;

    public function forgetCourseCache(?int $courseId = null, ?int $instructorId = null): void;
}

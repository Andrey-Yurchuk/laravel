<?php

namespace Tests\Performance;

use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CachePerformanceTest extends TestCase
{
    use RefreshDatabase;

    private CategoryServiceInterface $categoryService;
    private CourseServiceInterface $courseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryService = app(CategoryServiceInterface::class);
        $this->courseService = app(CourseServiceInterface::class);
    }

    public function test_categories_performance(): void
    {
        $iterations = 100;
        
        Cache::flush();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        for ($i = 0; $i < $iterations; $i++) {
            Cache::forget('categories:all');
            $this->categoryService->getAll();
        }
        
        $timeWithoutCache = microtime(true) - $startTime;
        $memoryWithoutCache = memory_get_usage() - $startMemory;
        
        $this->categoryService->getAll();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        for ($i = 0; $i < $iterations; $i++) {
            $this->categoryService->getAll();
        }
        
        $timeWithCache = microtime(true) - $startTime;
        $memoryWithCache = memory_get_usage() - $startMemory;
        
        $this->assertLessThan($timeWithoutCache, $timeWithCache, 
            "Кэш должен ускорить выполнение. Без кэша: {$timeWithoutCache}s, С кэшем: {$timeWithCache}s");
        
        $improvement = (($timeWithoutCache - $timeWithCache) / $timeWithoutCache) * 100;
        
        $this->info("Категории - Улучшение производительности: " . number_format($improvement, 2) . "%");
        $this->info("Без кэша: " . number_format($timeWithoutCache, 4) . "s");
        $this->info("С кэшем: " . number_format($timeWithCache, 4) . "s");
    }

    public function test_course_by_id_performance(): void
    {
        $course = \App\Models\Course::factory()->create();
        $iterations = 100;
        
        Cache::flush();
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            Cache::forget("course:{$course->id}");
            $this->courseService->getById($course->id);
        }
        
        $timeWithoutCache = microtime(true) - $startTime;
        
        $this->courseService->getById($course->id);
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $this->courseService->getById($course->id);
        }
        
        $timeWithCache = microtime(true) - $startTime;
        
        $this->assertLessThan($timeWithoutCache, $timeWithCache);
        
        $improvement = (($timeWithoutCache - $timeWithCache) / $timeWithoutCache) * 100;
        
        $this->info("Курс по ID - Улучшение производительности: " . number_format($improvement, 2) . "%");
        $this->info("Без кэша: " . number_format($timeWithoutCache, 4) . "s");
        $this->info("С кэшем: " . number_format($timeWithCache, 4) . "s");
    }

    public function test_recent_courses_performance(): void
    {
        \App\Models\Course::factory()->count(20)->create();
        $iterations = 100;
        $limit = 10;
        
        Cache::flush();
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            Cache::forget("courses:recent:{$limit}");
            $this->courseService->getRecent($limit);
        }
        
        $timeWithoutCache = microtime(true) - $startTime;
        
        $this->courseService->getRecent($limit);
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $this->courseService->getRecent($limit);
        }
        
        $timeWithCache = microtime(true) - $startTime;
        
        $this->assertLessThan($timeWithoutCache, $timeWithCache);
        
        $improvement = (($timeWithoutCache - $timeWithCache) / $timeWithoutCache) * 100;
        
        $this->info("Последние курсы - Улучшение производительности: " . number_format($improvement, 2) . "%");
        $this->info("Без кэша: " . number_format($timeWithoutCache, 4) . "s");
        $this->info("С кэшем: " . number_format($timeWithCache, 4) . "s");
    }

    public function test_mixed_load_performance(): void
    {
        $categories = \App\Models\Category::factory()->count(5)->create();
        $courses = \App\Models\Course::factory()->count(10)->create();
        
        $iterations = 50;
        
        Cache::flush();
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            Cache::forget('categories:all');
            $this->categoryService->getAll();
            
            Cache::forget("category:{$categories->random()->id}");
            $this->categoryService->getById($categories->random()->id);
            
            Cache::forget("course:{$courses->random()->id}");
            $this->courseService->getById($courses->random()->id);
            
            Cache::forget('courses:recent:5');
            $this->courseService->getRecent(5);
            
            Cache::forget('courses:count:published');
            $this->courseService->countPublished();
        }
        
        $timeWithoutCache = microtime(true) - $startTime;
        
        $this->categoryService->getAll();
        foreach ($categories as $category) {
            $this->categoryService->getById($category->id);
        }
        foreach ($courses as $course) {
            $this->courseService->getById($course->id);
        }
        $this->courseService->getRecent(5);
        $this->courseService->countPublished();
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $this->categoryService->getAll();
            $this->categoryService->getById($categories->random()->id);
            $this->courseService->getById($courses->random()->id);
            $this->courseService->getRecent(5);
            $this->courseService->countPublished();
        }
        
        $timeWithCache = microtime(true) - $startTime;
        
        $this->assertLessThan($timeWithoutCache, $timeWithCache);
        
        $improvement = (($timeWithoutCache - $timeWithCache) / $timeWithoutCache) * 100;
        
        $this->info("Смешанная нагрузка - Улучшение производительности: " . number_format($improvement, 2) . "%");
        $this->info("Без кэша: " . number_format($timeWithoutCache, 4) . "s");
        $this->info("С кэшем: " . number_format($timeWithCache, 4) . "s");
    }

    private function info(string $message): void
    {
        echo "\n" . $message . "\n";
    }
}


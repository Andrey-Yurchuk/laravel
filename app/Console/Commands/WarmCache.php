<?php

namespace App\Console\Commands;

use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\CourseServiceInterface;
use Illuminate\Console\Command;

class WarmCache extends Command
{
    protected $signature = 'cache:warm';

    protected $description = 'Прогрев кэша приложения (категории, курсы, статистика)';

    public function __construct(
        private CategoryServiceInterface $categoryService,
        private CourseServiceInterface $courseService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Начинаем прогрев кэша...');

        try {
            $this->warmCategories();
            $this->warmCourses();
            $this->warmStatistics();

            $this->info('Прогрев кэша завершен успешно!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Ошибка при прогреве кэша: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function warmCategories(): void
    {
        $this->info('Прогрев категорий...');

        $this->categoryService->getAll();
        $this->line('  Список категорий');

        $this->categoryService->count();
        $this->line('  Количество категорий');

        $categories = $this->categoryService->getAll();
        $warmedCount = 0;
        /** @var \App\Models\Category $category */
        foreach ($categories as $category) {
            try {
                $this->categoryService->getById($category->id);
                $warmedCount++;
            } catch (\Exception $e) {
                continue;
            }
        }
        $this->line("  Прогрев {$warmedCount} категорий");
    }

    private function warmCourses(): void
    {
        $this->info('Прогрев курсов...');

        $limits = [5, 10, 15, 20];
        foreach ($limits as $limit) {
            try {
                $this->courseService->getRecent($limit);
                $this->line("  Последние {$limit} курсов");
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            $recentCourses = $this->courseService->getRecent(10);
            $warmedCount = 0;
            /** @var \App\Models\Course $course */
            foreach ($recentCourses as $course) {
                try {
                    $this->courseService->getById($course->id);
                    $warmedCount++;
                } catch (\Exception $e) {
                    continue;
                }
            }
            $this->line("  Прогрев {$warmedCount} популярных курсов");
        } catch (\Exception $e) {
            $this->line('  Нет курсов для прогрева');
        }
    }

    private function warmStatistics(): void
    {
        $this->info('Прогрев статистики...');

        try {
            $this->courseService->countPublished();
            $this->line('  Количество опубликованных курсов');
        } catch (\Exception $e) {
            $this->line('  Не удалось прогреть количество курсов');
        }

        try {
            $instructors = $this->courseService->getInstructors();
            $warmedCount = 0;
            /** @var \App\Models\User $instructor */
            foreach ($instructors as $instructor) {
                try {
                    $this->courseService->countPublishedByInstructorId($instructor->id);
                    $warmedCount++;
                } catch (\Exception $e) {
                    continue;
                }
            }
            $this->line("  Статистика для {$warmedCount} инструкторов");
        } catch (\Exception $e) {
            $this->line('  Нет инструкторов для прогрева');
        }
    }
}

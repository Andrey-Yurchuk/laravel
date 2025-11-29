<?php

namespace App\Providers;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\CourseRepositoryInterface;
use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\CourseServiceInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\CourseRepository;
use App\Services\CategoryService;
use App\Services\CourseService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);

        // Services
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(CourseServiceInterface::class, CourseService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\CourseRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\CacheServiceInterface;
use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\CourseServiceInterface;
use App\Contracts\Services\SubscriptionServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Domain\Repositories\SubscriptionRepositoryInterface;
use App\Infrastructure\Repositories\EloquentSubscriptionRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CourseRepository;
use App\Repositories\UserRepository;
use App\Services\CacheService;
use App\Services\CategoryService;
use App\Services\CourseService;
use App\Services\SubscriptionService;
use App\Services\UserService;
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
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(SubscriptionRepositoryInterface::class, EloquentSubscriptionRepository::class);

        // Services
        $this->app->bind(CacheServiceInterface::class, CacheService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(CourseServiceInterface::class, CourseService::class);
        $this->app->bind(SubscriptionServiceInterface::class, SubscriptionService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

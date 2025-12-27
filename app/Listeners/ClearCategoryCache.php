<?php

namespace App\Listeners;

use App\Contracts\Services\CacheServiceInterface;
use App\Events\CategoryCreated;
use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;

class ClearCategoryCache
{
    public function __construct(
        private CacheServiceInterface $cacheService
    ) {
    }

    public function handle(CategoryCreated|CategoryUpdated|CategoryDeleted $event): void
    {
        $this->cacheService->forgetCategoryCache($event->category->id);
    }
}

<?php

namespace App\Events;

use App\Models\Category;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryDeleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Category $category
    ) {
    }
}

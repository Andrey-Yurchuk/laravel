<?php

namespace App\Events;

use App\Models\Category;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Category $category
    ) {
    }
}

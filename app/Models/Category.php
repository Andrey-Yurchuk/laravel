<?php

namespace App\Models;

use App\Events\CategoryCreated;
use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function booted(): void
    {
        static::created(function ($category) {
            event(new CategoryCreated($category));
        });

        static::updated(function ($category) {
            event(new CategoryUpdated($category));
        });

        static::deleted(function ($category) {
            event(new CategoryDeleted($category));
        });
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}

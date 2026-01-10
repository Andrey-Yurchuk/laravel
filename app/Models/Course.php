<?php

namespace App\Models;

use App\Enums\CourseDifficulty;
use App\Enums\CourseStatus;
use App\Events\CourseCreated;
use App\Events\CourseDeleted;
use App\Events\CourseUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $instructor_id
 * @property int $category_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property CourseDifficulty $difficulty_level
 * @property CourseStatus $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read User $instructor
 * @property-read Category $category
 */
class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'description',
        'difficulty_level',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'difficulty_level' => CourseDifficulty::class,
            'status' => CourseStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::created(function ($course) {
            event(new CourseCreated($course));
        });

        static::updated(function ($course) {
            event(new CourseUpdated($course));
        });

        static::deleted(function ($course) {
            event(new CourseDeleted($course));
        });
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(CoursePlan::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}

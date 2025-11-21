<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'plan_id',
        'status',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(CoursePlan::class, 'plan_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function learningProgress(): HasMany
    {
        return $this->hasMany(LearningProgress::class);
    }
}

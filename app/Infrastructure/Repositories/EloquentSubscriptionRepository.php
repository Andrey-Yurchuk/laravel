<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Aggregates\Subscription\Subscription;
use App\Domain\Repositories\SubscriptionRepositoryInterface;
use App\Domain\ValueObjects\SubscriptionId;
use App\Domain\ValueObjects\UserId;
use App\Domain\ValueObjects\CourseId;
use App\Domain\ValueObjects\PlanId;
use App\Domain\ValueObjects\SubscriptionPeriod;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription as SubscriptionModel;
use DateTimeImmutable;

class EloquentSubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function save(Subscription $subscription): void
    {
        SubscriptionModel::updateOrCreate(
            ['id' => $subscription->id()->value()],
            [
                'user_id' => $subscription->userId()->value(),
                'course_id' => $subscription->courseId()->value(),
                'plan_id' => $subscription->planId()->value(),
                'status' => $subscription->status(),
                'current_period_start' => $subscription->period()->start(),
                'current_period_end' => $subscription->period()->end(),
                'cancelled_at' => $subscription->cancelledAt(),
            ]
        );
    }

    public function findById(SubscriptionId $id): ?Subscription
    {
        $model = SubscriptionModel::find($id->value());

        if (!$model) {
            return null;
        }

        return $this->mapToAggregate($model);
    }

    public function findByUserId(UserId $userId): array
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, SubscriptionModel> $models */
        $models = SubscriptionModel::where('user_id', $userId->value())->get();

        return $models->map(function (SubscriptionModel $model) {
            return $this->mapToAggregate($model);
        })->toArray();
    }

    public function findActiveByUserId(UserId $userId): array
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, SubscriptionModel> $models */
        $models = SubscriptionModel::where('user_id', $userId->value())
            ->where('status', SubscriptionStatus::Active)
            ->get();

        return $models->map(function (SubscriptionModel $model) {
            return $this->mapToAggregate($model);
        })->toArray();
    }

    public function delete(Subscription $subscription): void
    {
        SubscriptionModel::destroy($subscription->id()->value());
    }

    /**
     * Преобразует Eloquent модель в агрегат
     */
    private function mapToAggregate(SubscriptionModel $model): Subscription
    {
        $periodStart = $model->current_period_start
            ? DateTimeImmutable::createFromMutable($model->current_period_start)
            : new DateTimeImmutable();

        $periodEnd = $model->current_period_end
            ? DateTimeImmutable::createFromMutable($model->current_period_end)
            : (new DateTimeImmutable())->modify('+1 month');

        return Subscription::reconstitute(
            new SubscriptionId($model->id),
            new UserId($model->user_id),
            new CourseId($model->course_id),
            new PlanId($model->plan_id),
            new SubscriptionPeriod($periodStart, $periodEnd),
            $model->status,
            $model->cancelled_at ? DateTimeImmutable::createFromMutable($model->cancelled_at) : null,
            null
        );
    }
}

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
use Carbon\Carbon;
use DateTimeImmutable;

class EloquentSubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function save(Subscription $subscription): void
    {
        $model = SubscriptionModel::find($subscription->id()->value());

        $periodStart = Carbon::instance($subscription->period()->start());
        $periodEnd = Carbon::instance($subscription->period()->end());
        $cancelledAt = $subscription->cancelledAt()
            ? Carbon::instance($subscription->cancelledAt())
            : null;

        if ($model) {
            $model->update([
                'user_id' => $subscription->userId()->value(),
                'course_id' => $subscription->courseId()->value(),
                'plan_id' => $subscription->planId()->value(),
                'status' => $subscription->status(),
                'current_period_start' => $periodStart,
                'current_period_end' => $periodEnd,
                'cancelled_at' => $cancelledAt,
            ]);
        } else {
            $model = new SubscriptionModel();
            $model->id = $subscription->id()->value();
            $model->user_id = $subscription->userId()->value();
            $model->course_id = $subscription->courseId()->value();
            $model->plan_id = $subscription->planId()->value();
            $model->status = $subscription->status();
            $model->current_period_start = $periodStart;
            $model->current_period_end = $periodEnd;
            $model->cancelled_at = $cancelledAt;
            $model->save();
        }
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

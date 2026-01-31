<?php

namespace App\Services;

use App\Contracts\Services\SubscriptionServiceInterface;
use App\Domain\Aggregates\Subscription\Subscription;
use App\Domain\Repositories\SubscriptionRepositoryInterface;
use App\Domain\ValueObjects\SubscriptionId;
use App\Domain\ValueObjects\UserId;
use App\Domain\ValueObjects\CourseId;
use App\Domain\ValueObjects\PlanId;
use App\Domain\ValueObjects\SubscriptionPeriod;
use Illuminate\Support\Str;

class SubscriptionService implements SubscriptionServiceInterface
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $repository
    ) {
    }

    public function createSubscription(
        UserId $userId,
        CourseId $courseId,
        PlanId $planId,
        SubscriptionPeriod $period
    ): Subscription {
        // Генерируем новый ID (в реальном приложении можно использовать UUID или автоинкремент)
        $id = new SubscriptionId($this->generateNextId());

        $subscription = Subscription::create(
            $id,
            $userId,
            $courseId,
            $planId,
            $period
        );

        $this->repository->save($subscription);

        return $subscription;
    }

    public function activateSubscription(SubscriptionId $id): Subscription
    {
        $subscription = $this->getSubscriptionOrFail($id);
        $subscription->activate();
        $this->repository->save($subscription);

        return $subscription;
    }

    public function cancelSubscription(SubscriptionId $id, string $reason): Subscription
    {
        $subscription = $this->getSubscriptionOrFail($id);
        $subscription->cancel($reason);
        $this->repository->save($subscription);

        return $subscription;
    }

    public function renewSubscription(SubscriptionId $id, SubscriptionPeriod $newPeriod): Subscription
    {
        $subscription = $this->getSubscriptionOrFail($id);
        $subscription->renew($newPeriod);
        $this->repository->save($subscription);

        return $subscription;
    }

    public function upgradeSubscription(SubscriptionId $id, PlanId $newPlanId): Subscription
    {
        $subscription = $this->getSubscriptionOrFail($id);
        $subscription->upgrade($newPlanId);
        $this->repository->save($subscription);

        return $subscription;
    }

    public function downgradeSubscription(SubscriptionId $id, PlanId $newPlanId): Subscription
    {
        $subscription = $this->getSubscriptionOrFail($id);
        $subscription->downgrade($newPlanId);
        $this->repository->save($subscription);

        return $subscription;
    }

    public function expireSubscription(SubscriptionId $id): Subscription
    {
        $subscription = $this->getSubscriptionOrFail($id);
        $subscription->expire();
        $this->repository->save($subscription);

        return $subscription;
    }

    public function hasActiveSubscription(UserId $userId, CourseId $courseId): bool
    {
        $subscriptions = $this->repository->findActiveByUserId($userId);

        foreach ($subscriptions as $subscription) {
            if ($subscription->courseId()->equals($courseId) && $subscription->isActive()) {
                return true;
            }
        }

        return false;
    }

    public function getActiveSubscriptions(UserId $userId): array
    {
        return $this->repository->findActiveByUserId($userId);
    }

    public function getAllSubscriptions(UserId $userId): array
    {
        return $this->repository->findByUserId($userId);
    }

    public function getSubscription(SubscriptionId $id): ?Subscription
    {
        return $this->repository->findById($id);
    }

    /**
     * Получить подписку или выбросить исключение
     */
    private function getSubscriptionOrFail(SubscriptionId $id): Subscription
    {
        $subscription = $this->repository->findById($id);

        if (!$subscription) {
            throw new \DomainException("Subscription with ID {$id->value()} not found");
        }

        return $subscription;
    }

    /**
     * Генерирует следующий ID для подписки
     * В реальном приложении это должно быть через автоинкремент БД или UUID
     */
    private function generateNextId(): int
    {
        // Простая реализация - в реальности нужно использовать автоинкремент БД
        $lastSubscription = \App\Models\Subscription::query()
            ->orderBy('id', 'desc')
            ->first();

        return $lastSubscription ? $lastSubscription->id + 1 : 1;
    }
}


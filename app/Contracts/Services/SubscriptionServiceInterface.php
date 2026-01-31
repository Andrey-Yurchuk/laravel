<?php

namespace App\Contracts\Services;

use App\Domain\Aggregates\Subscription\Subscription;
use App\Domain\ValueObjects\SubscriptionId;
use App\Domain\ValueObjects\UserId;
use App\Domain\ValueObjects\CourseId;
use App\Domain\ValueObjects\PlanId;
use App\Domain\ValueObjects\SubscriptionPeriod;

interface SubscriptionServiceInterface
{
    /**
     * Создать новую подписку
     */
    public function createSubscription(
        UserId $userId,
        CourseId $courseId,
        PlanId $planId,
        SubscriptionPeriod $period
    ): Subscription;

    /**
     * Активировать подписку
     */
    public function activateSubscription(SubscriptionId $id): Subscription;

    /**
     * Отменить подписку
     */
    public function cancelSubscription(SubscriptionId $id, string $reason): Subscription;

    /**
     * Продлить подписку
     */
    public function renewSubscription(SubscriptionId $id, SubscriptionPeriod $newPeriod): Subscription;

    /**
     * Обновить план подписки (upgrade)
     */
    public function upgradeSubscription(SubscriptionId $id, PlanId $newPlanId): Subscription;

    /**
     * Обновить план подписки (downgrade)
     */
    public function downgradeSubscription(SubscriptionId $id, PlanId $newPlanId): Subscription;

    /**
     * Истечение подписки
     */
    public function expireSubscription(SubscriptionId $id): Subscription;

    /**
     * Проверить, есть ли у пользователя активная подписка на курс
     */
    public function hasActiveSubscription(UserId $userId, CourseId $courseId): bool;

    /**
     * Получить активные подписки пользователя
     */
    public function getActiveSubscriptions(UserId $userId): array;

    /**
     * Получить все подписки пользователя (любого статуса)
     */
    public function getAllSubscriptions(UserId $userId): array;

    /**
     * Получить подписку по id
     */
    public function getSubscription(SubscriptionId $id): ?Subscription;
}

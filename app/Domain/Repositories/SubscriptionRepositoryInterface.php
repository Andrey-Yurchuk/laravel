<?php

namespace App\Domain\Repositories;

use App\Domain\Aggregates\Subscription\Subscription;
use App\Domain\ValueObjects\SubscriptionId;
use App\Domain\ValueObjects\UserId;

interface SubscriptionRepositoryInterface
{
    /**
     * Сохранить или обновить подписку
     */
    public function save(Subscription $subscription): void;

    /**
     * Найти подписку по id
     */
    public function findById(SubscriptionId $id): ?Subscription;

    /**
     * Найти все подписки пользователя
     */
    public function findByUserId(UserId $userId): array;

    /**
     * Удалить подписку
     */
    public function delete(Subscription $subscription): void;

    /**
     * Найти активные подписки пользователя
     */
    public function findActiveByUserId(UserId $userId): array;
}

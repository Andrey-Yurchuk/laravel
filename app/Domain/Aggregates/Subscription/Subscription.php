<?php

namespace App\Domain\Aggregates\Subscription;

use App\Domain\ValueObjects\SubscriptionId;
use App\Domain\ValueObjects\UserId;
use App\Domain\ValueObjects\CourseId;
use App\Domain\ValueObjects\PlanId;
use App\Domain\ValueObjects\SubscriptionPeriod;
use App\Enums\SubscriptionStatus;
use DateTimeImmutable;
use DomainException;

final class Subscription
{
    private SubscriptionStatus $status;
    private ?DateTimeImmutable $cancelledAt = null;
    private ?string $cancellationReason = null;

    private function __construct(
        private readonly SubscriptionId $id,
        private readonly UserId $userId,
        private readonly CourseId $courseId,
        private PlanId $planId,
        private SubscriptionPeriod $period
    ) {
        $this->status = SubscriptionStatus::Pending;
    }

    public static function create(
        SubscriptionId $id,
        UserId $userId,
        CourseId $courseId,
        PlanId $planId,
        SubscriptionPeriod $period
    ): self {
        return new self($id, $userId, $courseId, $planId, $period);
    }

    public static function reconstitute(
        SubscriptionId $id,
        UserId $userId,
        CourseId $courseId,
        PlanId $planId,
        SubscriptionPeriod $period,
        SubscriptionStatus $status,
        ?DateTimeImmutable $cancelledAt = null,
        ?string $cancellationReason = null
    ): self {
        $subscription = new self($id, $userId, $courseId, $planId, $period);
        $subscription->status = $status;
        $subscription->cancelledAt = $cancelledAt;
        $subscription->cancellationReason = $cancellationReason;

        return $subscription;
    }

    public function id(): SubscriptionId
    {
        return $this->id;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function courseId(): CourseId
    {
        return $this->courseId;
    }

    public function planId(): PlanId
    {
        return $this->planId;
    }

    public function period(): SubscriptionPeriod
    {
        return $this->period;
    }

    public function status(): SubscriptionStatus
    {
        return $this->status;
    }

    public function cancelledAt(): ?DateTimeImmutable
    {
        return $this->cancelledAt;
    }

    public function cancellationReason(): ?string
    {
        return $this->cancellationReason;
    }

    public function activate(): void
    {
        if ($this->status !== SubscriptionStatus::Pending) {
            throw new DomainException(
                "Only pending subscriptions can be activated. "
                . "Current status: {$this->status->value}"
            );
        }

        $this->status = SubscriptionStatus::Active;
    }

    public function cancel(string $reason): void
    {
        if ($this->status === SubscriptionStatus::Cancelled) {
            throw new DomainException("Subscription is already cancelled");
        }

        if ($this->status === SubscriptionStatus::Expired) {
            throw new DomainException("Cannot cancel expired subscription");
        }

        $this->status = SubscriptionStatus::Cancelled;
        $this->cancelledAt = new DateTimeImmutable();
        $this->cancellationReason = $reason;
    }

    public function renew(SubscriptionPeriod $newPeriod): void
    {
        if ($this->status !== SubscriptionStatus::Active) {
            throw new DomainException(
                "Only active subscriptions can be renewed. "
                . "Current status: {$this->status->value}"
            );
        }

        $this->period = $newPeriod;
    }

    public function upgrade(PlanId $newPlanId): void
    {
        if ($this->status !== SubscriptionStatus::Active) {
            throw new DomainException(
                "Only active subscriptions can be upgraded. "
                . "Current status: {$this->status->value}"
            );
        }

        if ($this->planId->equals($newPlanId)) {
            throw new DomainException("Cannot upgrade to the same plan");
        }

        $this->planId = $newPlanId;
    }

    public function downgrade(PlanId $newPlanId): void
    {
        if ($this->status !== SubscriptionStatus::Active) {
            throw new DomainException(
                "Only active subscriptions can be downgraded. "
                . "Current status: {$this->status->value}"
            );
        }

        if ($this->planId->equals($newPlanId)) {
            throw new DomainException("Cannot downgrade to the same plan");
        }

        $this->planId = $newPlanId;
    }

    public function expire(): void
    {
        if ($this->status === SubscriptionStatus::Cancelled) {
            return;
        }

        if ($this->status === SubscriptionStatus::Expired) {
            return;
        }

        $this->status = SubscriptionStatus::Expired;
    }

    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::Active
            && $this->period->isActive(new DateTimeImmutable());
    }

    public function canBeRenewed(): bool
    {
        return $this->status === SubscriptionStatus::Active;
    }

    public function canBeCancelled(): bool
    {
        return $this->status !== SubscriptionStatus::Cancelled
            && $this->status !== SubscriptionStatus::Expired;
    }
}

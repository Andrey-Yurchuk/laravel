<?php

namespace Tests\Unit\Domain\Aggregates\Subscription;

use App\Domain\Aggregates\Subscription\Subscription;
use App\Domain\ValueObjects\SubscriptionId;
use App\Domain\ValueObjects\UserId;
use App\Domain\ValueObjects\CourseId;
use App\Domain\ValueObjects\PlanId;
use App\Domain\ValueObjects\SubscriptionPeriod;
use App\Enums\SubscriptionStatus;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    private function createSubscriptionPeriod(): SubscriptionPeriod
    {
        $start = new \DateTimeImmutable('2024-01-01');
        $end = new \DateTimeImmutable('2024-02-01');
        return new SubscriptionPeriod($start, $end);
    }

    public function test_can_create_subscription(): void
    {
        $subscription = Subscription::create(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod()
        );

        $this->assertEquals(SubscriptionStatus::Pending, $subscription->status());
        $this->assertEquals(1, $subscription->id()->value());
        $this->assertEquals(1, $subscription->userId()->value());
        $this->assertEquals(1, $subscription->courseId()->value());
        $this->assertEquals(1, $subscription->planId()->value());
    }

    public function test_can_activate_pending_subscription(): void
    {
        $subscription = Subscription::create(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod()
        );

        $subscription->activate();

        $this->assertEquals(SubscriptionStatus::Active, $subscription->status());
    }

    public function test_cannot_activate_non_pending_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Active
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Only pending subscriptions can be activated');

        $subscription->activate();
    }

    public function test_can_cancel_active_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Active
        );

        $subscription->cancel('User request');

        $this->assertEquals(SubscriptionStatus::Cancelled, $subscription->status());
        $this->assertNotNull($subscription->cancelledAt());
        $this->assertEquals('User request', $subscription->cancellationReason());
    }

    public function test_cannot_cancel_already_cancelled_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Cancelled
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Subscription is already cancelled');

        $subscription->cancel('User request');
    }

    public function test_cannot_cancel_expired_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Expired
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Cannot cancel expired subscription');

        $subscription->cancel('User request');
    }

    public function test_can_renew_active_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Active
        );

        $newPeriod = new SubscriptionPeriod(
            new \DateTimeImmutable('2024-02-01'),
            new \DateTimeImmutable('2024-03-01')
        );

        $subscription->renew($newPeriod);

        $this->assertEquals($newPeriod->start(), $subscription->period()->start());
        $this->assertEquals($newPeriod->end(), $subscription->period()->end());
    }

    public function test_cannot_renew_non_active_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Pending
        );

        $newPeriod = new SubscriptionPeriod(
            new \DateTimeImmutable('2024-02-01'),
            new \DateTimeImmutable('2024-03-01')
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Only active subscriptions can be renewed');

        $subscription->renew($newPeriod);
    }

    public function test_can_upgrade_active_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Active
        );

        $newPlanId = new PlanId(2);
        $subscription->upgrade($newPlanId);

        $this->assertTrue($subscription->planId()->equals($newPlanId));
    }

    public function test_cannot_upgrade_to_same_plan(): void
    {
        $planId = new PlanId(1);
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            $planId,
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Active
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Cannot upgrade to the same plan');

        $subscription->upgrade($planId);
    }

    public function test_can_downgrade_active_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(2),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Active
        );

        $newPlanId = new PlanId(1);
        $subscription->downgrade($newPlanId);

        $this->assertTrue($subscription->planId()->equals($newPlanId));
    }

    public function test_can_expire_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Active
        );

        $subscription->expire();

        $this->assertEquals(SubscriptionStatus::Expired, $subscription->status());
    }

    public function test_expire_does_nothing_for_already_expired_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Expired
        );

        $subscription->expire();

        $this->assertEquals(SubscriptionStatus::Expired, $subscription->status());
    }

    public function test_is_active_returns_true_for_active_subscription_in_period(): void
    {
        $now = new \DateTimeImmutable();
        $start = $now->modify('-1 day');
        $end = $now->modify('+30 days');
        $period = new SubscriptionPeriod($start, $end);

        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $period,
            SubscriptionStatus::Active
        );

        $this->assertTrue($subscription->isActive());
    }

    public function test_can_be_renewed_returns_true_for_active_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Active
        );

        $this->assertTrue($subscription->canBeRenewed());
    }

    public function test_can_be_cancelled_returns_true_for_active_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Active
        );

        $this->assertTrue($subscription->canBeCancelled());
    }

    public function test_can_be_cancelled_returns_false_for_cancelled_subscription(): void
    {
        $subscription = Subscription::reconstitute(
            new SubscriptionId(1),
            new UserId(1),
            new CourseId(1),
            new PlanId(1),
            $this->createSubscriptionPeriod(),
            SubscriptionStatus::Cancelled
        );

        $this->assertFalse($subscription->canBeCancelled());
    }
}

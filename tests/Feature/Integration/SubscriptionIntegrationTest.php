<?php

namespace Tests\Feature\Integration;

use App\Contracts\Services\SubscriptionServiceInterface;
use App\Domain\ValueObjects\CourseId;
use App\Domain\ValueObjects\PlanId;
use App\Domain\ValueObjects\SubscriptionId;
use App\Domain\ValueObjects\SubscriptionPeriod;
use App\Domain\ValueObjects\UserId;
use App\Enums\SubscriptionStatus;
use App\Models\Course;
use App\Models\CoursePlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionServiceInterface $subscriptionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subscriptionService = app(SubscriptionServiceInterface::class);
    }

    public function test_full_subscription_lifecycle(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $plan = CoursePlan::factory()->create(['course_id' => $course->id]);

        $subscription = $this->subscriptionService->createSubscription(
            new UserId($user->id),
            new CourseId($course->id),
            new PlanId($plan->id),
            new SubscriptionPeriod(
                new \DateTimeImmutable('today'),
                new \DateTimeImmutable('+31 days')
            )
        );

        $this->assertEquals(SubscriptionStatus::Pending, $subscription->status());
        $this->assertEquals($user->id, $subscription->userId()->value());
        $this->assertEquals($course->id, $subscription->courseId()->value());

        $activated = $this->subscriptionService->activateSubscription(
            $subscription->id()
        );

        $this->assertEquals(SubscriptionStatus::Active, $activated->status());
        $this->assertTrue($activated->isActive());

        $hasActive = $this->subscriptionService->hasActiveSubscription(
            new UserId($user->id),
            new CourseId($course->id)
        );

        $this->assertTrue($hasActive);

        $cancelled = $this->subscriptionService->cancelSubscription(
            $subscription->id(),
            'User requested cancellation'
        );

        $this->assertEquals(SubscriptionStatus::Cancelled, $cancelled->status());
        $this->assertNotNull($cancelled->cancelledAt());
        $this->assertFalse($cancelled->isActive());
    }

    public function test_cannot_activate_already_cancelled_subscription(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $plan = CoursePlan::factory()->create(['course_id' => $course->id]);

        $subscription = $this->subscriptionService->createSubscription(
            new UserId($user->id),
            new CourseId($course->id),
            new PlanId($plan->id),
            new SubscriptionPeriod(
                new \DateTimeImmutable('today'),
                new \DateTimeImmutable('+31 days')
            )
        );

        $this->subscriptionService->cancelSubscription(
            $subscription->id(),
            'Test cancellation'
        );

        $this->expectException(\DomainException::class);
        $this->subscriptionService->activateSubscription($subscription->id());
    }

    public function test_can_renew_active_subscription(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $plan = CoursePlan::factory()->create(['course_id' => $course->id]);

        $subscription = $this->subscriptionService->createSubscription(
            new UserId($user->id),
            new CourseId($course->id),
            new PlanId($plan->id),
            new SubscriptionPeriod(
                new \DateTimeImmutable('today'),
                new \DateTimeImmutable('+31 days')
            )
        );

        $this->subscriptionService->activateSubscription($subscription->id());

        $newPeriod = new SubscriptionPeriod(
            new \DateTimeImmutable('+32 days'),
            new \DateTimeImmutable('+63 days')
        );

        $renewed = $this->subscriptionService->renewSubscription(
            $subscription->id(),
            $newPeriod
        );

        $this->assertEquals(
            (new \DateTimeImmutable('+32 days'))->format('Y-m-d'),
            $renewed->period()->start()->format('Y-m-d')
        );
        $this->assertEquals(
            (new \DateTimeImmutable('+63 days'))->format('Y-m-d'),
            $renewed->period()->end()->format('Y-m-d')
        );
    }
}

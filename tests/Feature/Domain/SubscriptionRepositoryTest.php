<?php

namespace Tests\Feature\Domain;

use App\Domain\Aggregates\Subscription\Subscription;
use App\Domain\Repositories\SubscriptionRepositoryInterface;
use App\Domain\ValueObjects\SubscriptionId;
use App\Domain\ValueObjects\UserId;
use App\Domain\ValueObjects\CourseId;
use App\Domain\ValueObjects\PlanId;
use App\Domain\ValueObjects\SubscriptionPeriod;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription as SubscriptionModel;
use App\Models\User;
use App\Models\Course;
use App\Models\CoursePlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(SubscriptionRepositoryInterface::class);
    }

    private function createSubscriptionPeriod(): SubscriptionPeriod
    {
        $start = new \DateTimeImmutable('+1 day');
        $end = new \DateTimeImmutable('+32 days');
        return new SubscriptionPeriod($start, $end);
    }

    public function test_repository_can_save_and_retrieve_subscription(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $plan = CoursePlan::factory()->create(['course_id' => $course->id]);

        $subscription = Subscription::create(
            new SubscriptionId(1),
            new UserId($user->id),
            new CourseId($course->id),
            new PlanId($plan->id),
            $this->createSubscriptionPeriod()
        );

        $this->repository->save($subscription);

        $this->assertDatabaseHas('subscriptions', [
            'id' => 1,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Pending->value,
        ]);

        $found = $this->repository->findById(new SubscriptionId(1));

        $this->assertNotNull($found);
        $this->assertInstanceOf(Subscription::class, $found);
        $this->assertEquals(1, $found->id()->value());
        $this->assertEquals($user->id, $found->userId()->value());
        $this->assertEquals($course->id, $found->courseId()->value());
        $this->assertEquals($plan->id, $found->planId()->value());
        $this->assertEquals(SubscriptionStatus::Pending, $found->status());
    }

    public function test_repository_can_update_subscription(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $plan = CoursePlan::factory()->create(['course_id' => $course->id]);

        $subscription = Subscription::create(
            new SubscriptionId(1),
            new UserId($user->id),
            new CourseId($course->id),
            new PlanId($plan->id),
            $this->createSubscriptionPeriod()
        );

        $this->repository->save($subscription);

        $subscription->activate();
        $this->repository->save($subscription);

        $found = $this->repository->findById(new SubscriptionId(1));
        $this->assertNotNull($found);
        $this->assertEquals(SubscriptionStatus::Active, $found->status());
    }

    public function test_repository_can_find_subscriptions_by_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $course = Course::factory()->create();
        $plan = CoursePlan::factory()->create(['course_id' => $course->id]);

        $sub1 = Subscription::create(
            new SubscriptionId(1),
            new UserId($user1->id),
            new CourseId($course->id),
            new PlanId($plan->id),
            $this->createSubscriptionPeriod()
        );
        $this->repository->save($sub1);

        $sub2 = Subscription::create(
            new SubscriptionId(2),
            new UserId($user1->id),
            new CourseId($course->id),
            new PlanId($plan->id),
            $this->createSubscriptionPeriod()
        );
        $this->repository->save($sub2);

        $sub3 = Subscription::create(
            new SubscriptionId(3),
            new UserId($user2->id),
            new CourseId($course->id),
            new PlanId($plan->id),
            $this->createSubscriptionPeriod()
        );
        $this->repository->save($sub3);

        $user1Subscriptions = $this->repository->findByUserId(new UserId($user1->id));

        $this->assertCount(2, $user1Subscriptions);
        foreach ($user1Subscriptions as $sub) {
            $this->assertEquals($user1->id, $sub->userId()->value());
        }
    }
}

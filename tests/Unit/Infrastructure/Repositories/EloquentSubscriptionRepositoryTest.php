<?php

namespace Tests\Unit\Infrastructure\Repositories;

use App\Domain\Aggregates\Subscription\Subscription;
use App\Domain\Repositories\SubscriptionRepositoryInterface;
use App\Domain\ValueObjects\SubscriptionId;
use App\Domain\ValueObjects\UserId;
use App\Domain\ValueObjects\CourseId;
use App\Domain\ValueObjects\PlanId;
use App\Domain\ValueObjects\SubscriptionPeriod;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription as SubscriptionModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentSubscriptionRepositoryTest extends TestCase
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
        $start = new \DateTimeImmutable('2024-01-01');
        $end = new \DateTimeImmutable('2024-02-01');
        return new SubscriptionPeriod($start, $end);
    }

    public function test_can_save_subscription(): void
    {
        $user = \App\Models\User::factory()->create();
        $course = \App\Models\Course::factory()->create();
        $plan = \App\Models\CoursePlan::factory()->create(['course_id' => $course->id]);

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
    }

    public function test_can_find_subscription_by_id(): void
    {
        $model = SubscriptionModel::factory()->active()->create([
            'current_period_start' => '2024-01-01 00:00:00',
            'current_period_end' => '2024-02-01 00:00:00',
        ]);

        $subscription = $this->repository->findById(new SubscriptionId($model->id));

        $this->assertNotNull($subscription);
        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals($model->id, $subscription->id()->value());
        $this->assertEquals(SubscriptionStatus::Active, $subscription->status());
    }

    public function test_returns_null_when_subscription_not_found(): void
    {
        $subscription = $this->repository->findById(new SubscriptionId(999));

        $this->assertNull($subscription);
    }

    public function test_can_find_subscriptions_by_user_id(): void
    {
        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();

        SubscriptionModel::factory()->count(3)->create([
            'user_id' => $user1->id,
        ]);

        SubscriptionModel::factory()->create([
            'user_id' => $user2->id,
        ]);

        $subscriptions = $this->repository->findByUserId(new UserId($user1->id));

        $this->assertCount(3, $subscriptions);
        foreach ($subscriptions as $subscription) {
            $this->assertInstanceOf(Subscription::class, $subscription);
            $this->assertEquals($user1->id, $subscription->userId()->value());
        }
    }

    public function test_can_find_active_subscriptions_by_user_id(): void
    {
        $user = \App\Models\User::factory()->create();

        SubscriptionModel::factory()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::Active,
        ]);

        SubscriptionModel::factory()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::Pending,
        ]);

        SubscriptionModel::factory()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::Cancelled,
        ]);

        $subscriptions = $this->repository->findActiveByUserId(new UserId($user->id));

        $this->assertCount(1, $subscriptions);
        $this->assertEquals(SubscriptionStatus::Active, $subscriptions[0]->status());
    }

    public function test_can_update_existing_subscription(): void
    {
        $user = \App\Models\User::factory()->create();
        $course = \App\Models\Course::factory()->create();
        $plan = \App\Models\CoursePlan::factory()->create(['course_id' => $course->id]);

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

    public function test_can_delete_subscription(): void
    {
        $model = SubscriptionModel::factory()->create();

        $subscription = $this->repository->findById(new SubscriptionId($model->id));
        $this->assertNotNull($subscription);

        $this->repository->delete($subscription);

        $this->assertDatabaseMissing('subscriptions', ['id' => $model->id]);
    }
}

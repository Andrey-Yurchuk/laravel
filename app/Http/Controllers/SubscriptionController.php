<?php

namespace App\Http\Controllers;

use App\Contracts\Services\SubscriptionServiceInterface;
use App\Domain\Aggregates\Subscription\Subscription;
use App\Domain\ValueObjects\CourseId;
use App\Domain\ValueObjects\PlanId;
use App\Domain\ValueObjects\SubscriptionId;
use App\Domain\ValueObjects\SubscriptionPeriod;
use App\Domain\ValueObjects\UserId;
use App\Http\Requests\Subscription\CancelSubscriptionRequest;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use DateTimeImmutable;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptionService
    ) {
    }

    /**
     * Получить все подписки текущего пользователя
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscriptions = $this->subscriptionService->getAllSubscriptions(
            new UserId($user->id)
        );

        return response()->json([
            'subscriptions' => array_map(
                fn($subscription) => $this->subscriptionToArray($subscription),
                $subscriptions
            ),
        ]);
    }

    /**
     * Создать новую подписку
     */
    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $user = $request->user();

        try {
            $subscription = $this->subscriptionService->createSubscription(
                new UserId($user->id),
                new CourseId($request->validated()['course_id']),
                new PlanId($request->validated()['plan_id']),
                new SubscriptionPeriod(
                    new DateTimeImmutable($request->validated()['period_start']),
                    new DateTimeImmutable($request->validated()['period_end'])
                )
            );

            return response()->json([
                'message' => 'Subscription created successfully',
                'subscription' => $this->subscriptionToArray($subscription),
            ], 201);
        } catch (DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Активировать подписку
     */
    public function activate(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $subscription = $this->subscriptionService->activateSubscription(
                new SubscriptionId($id)
            );

            return response()->json([
                'message' => 'Subscription activated successfully',
                'subscription' => $this->subscriptionToArray($subscription),
            ]);
        } catch (DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Отменить подписку
     */
    public function cancel(CancelSubscriptionRequest $request, int $id): JsonResponse
    {
        try {
            $subscription = $this->subscriptionService->cancelSubscription(
                new SubscriptionId($id),
                $request->validated()['reason']
            );

            return response()->json([
                'message' => 'Subscription cancelled successfully',
                'subscription' => $this->subscriptionToArray($subscription),
            ]);
        } catch (DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Преобразует агрегат Subscription в массив для JSON ответа
     */
    private function subscriptionToArray(Subscription $subscription): array
    {
        return [
            'id' => $subscription->id()->value(),
            'course_id' => $subscription->courseId()->value(),
            'plan_id' => $subscription->planId()->value(),
            'status' => $subscription->status()->value,
            'period_start' => $subscription->period()->start()->format('Y-m-d H:i:s'),
            'period_end' => $subscription->period()->end()->format('Y-m-d H:i:s'),
            'is_active' => $subscription->isActive(),
            'cancelled_at' => $subscription->cancelledAt()?->format('Y-m-d H:i:s'),
        ];
    }
}

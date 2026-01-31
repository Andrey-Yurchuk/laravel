<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\SubscriptionPeriod;
use PHPUnit\Framework\TestCase;

class SubscriptionPeriodTest extends TestCase
{
    public function test_can_create_subscription_period(): void
    {
        $start = new \DateTimeImmutable('2024-01-01');
        $end = new \DateTimeImmutable('2024-02-01');
        
        $period = new SubscriptionPeriod($start, $end);
        
        $this->assertEquals($start, $period->start());
        $this->assertEquals($end, $period->end());
    }

    public function test_throws_exception_when_start_equals_end(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Start date must be before end date');
        
        $date = new \DateTimeImmutable('2024-01-01');
        new SubscriptionPeriod($date, $date);
    }

    public function test_throws_exception_when_start_after_end(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Start date must be before end date');
        
        $start = new \DateTimeImmutable('2024-02-01');
        $end = new \DateTimeImmutable('2024-01-01');
        
        new SubscriptionPeriod($start, $end);
    }

    public function test_is_active_returns_true_when_current_date_is_in_period(): void
    {
        $start = new \DateTimeImmutable('2024-01-01');
        $end = new \DateTimeImmutable('2024-02-01');
        $period = new SubscriptionPeriod($start, $end);
        
        $now = new \DateTimeImmutable('2024-01-15');
        
        $this->assertTrue($period->isActive($now));
    }

    public function test_is_active_returns_false_when_current_date_is_before_period(): void
    {
        $start = new \DateTimeImmutable('2024-01-01');
        $end = new \DateTimeImmutable('2024-02-01');
        $period = new SubscriptionPeriod($start, $end);
        
        $now = new \DateTimeImmutable('2023-12-31');
        
        $this->assertFalse($period->isActive($now));
    }

    public function test_is_active_returns_false_when_current_date_is_after_period(): void
    {
        $start = new \DateTimeImmutable('2024-01-01');
        $end = new \DateTimeImmutable('2024-02-01');
        $period = new SubscriptionPeriod($start, $end);
        
        $now = new \DateTimeImmutable('2024-02-02');
        
        $this->assertFalse($period->isActive($now));
    }

    public function test_days_remaining_returns_correct_value(): void
    {
        $start = new \DateTimeImmutable('2024-01-01');
        $end = new \DateTimeImmutable('2024-01-31');
        $period = new SubscriptionPeriod($start, $end);
        
        $now = new \DateTimeImmutable('2024-01-15');
        
        $this->assertEquals(16, $period->daysRemaining($now));
    }

    public function test_days_remaining_returns_zero_when_period_expired(): void
    {
        $start = new \DateTimeImmutable('2024-01-01');
        $end = new \DateTimeImmutable('2024-01-31');
        $period = new SubscriptionPeriod($start, $end);
        
        $now = new \DateTimeImmutable('2024-02-01');
        
        $this->assertEquals(0, $period->daysRemaining($now));
    }

    public function test_duration_in_days_returns_correct_value(): void
    {
        $start = new \DateTimeImmutable('2024-01-01');
        $end = new \DateTimeImmutable('2024-01-31');
        $period = new SubscriptionPeriod($start, $end);
        
        $this->assertEquals(30, $period->durationInDays());
    }
}

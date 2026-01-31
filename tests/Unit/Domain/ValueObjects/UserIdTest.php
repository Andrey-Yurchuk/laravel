<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function test_can_create_user_id(): void
    {
        $userId = new UserId(1);
        
        $this->assertEquals(1, $userId->value());
        $this->assertEquals('1', (string) $userId);
    }

    public function test_throws_exception_for_zero_id(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User ID must be positive');
        
        new UserId(0);
    }

    public function test_throws_exception_for_negative_id(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User ID must be positive');
        
        new UserId(-1);
    }

    public function test_equals_returns_true_for_same_id(): void
    {
        $userId1 = new UserId(1);
        $userId2 = new UserId(1);
        
        $this->assertTrue($userId1->equals($userId2));
    }

    public function test_equals_returns_false_for_different_ids(): void
    {
        $userId1 = new UserId(1);
        $userId2 = new UserId(2);
        
        $this->assertFalse($userId1->equals($userId2));
    }
}

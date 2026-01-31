<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_can_create_money(): void
    {
        $money = new Money(1000, 'USD');
        
        $this->assertEquals(1000, $money->amount());
        $this->assertEquals('USD', $money->currency());
        $this->assertEquals(10.0, $money->toDecimal());
    }

    public function test_throws_exception_for_negative_amount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount cannot be negative');
        
        new Money(-100, 'USD');
    }

    public function test_throws_exception_for_unsupported_currency(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported currency');
        
        new Money(1000, 'BTC');
    }

    public function test_can_add_money_with_same_currency(): void
    {
        $money1 = new Money(1000, 'USD');
        $money2 = new Money(500, 'USD');
        
        $result = $money1->add($money2);
        
        $this->assertEquals(1500, $result->amount());
        $this->assertEquals('USD', $result->currency());
    }

    public function test_throws_exception_when_adding_different_currencies(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot add different currencies');
        
        $money1 = new Money(1000, 'USD');
        $money2 = new Money(500, 'EUR');
        
        $money1->add($money2);
    }

    public function test_can_subtract_money(): void
    {
        $money1 = new Money(1000, 'USD');
        $money2 = new Money(300, 'USD');
        
        $result = $money1->subtract($money2);
        
        $this->assertEquals(700, $result->amount());
    }

    public function test_throws_exception_when_subtract_results_in_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Result cannot be negative');
        
        $money1 = new Money(100, 'USD');
        $money2 = new Money(200, 'USD');
        
        $money1->subtract($money2);
    }

    public function test_equals_returns_true_for_same_money(): void
    {
        $money1 = new Money(1000, 'USD');
        $money2 = new Money(1000, 'USD');
        
        $this->assertTrue($money1->equals($money2));
    }

    public function test_is_greater_than_works_correctly(): void
    {
        $money1 = new Money(1000, 'USD');
        $money2 = new Money(500, 'USD');
        
        $this->assertTrue($money1->isGreaterThan($money2));
        $this->assertFalse($money2->isGreaterThan($money1));
    }

    public function test_to_string_formats_correctly(): void
    {
        $money = new Money(1999, 'USD');
        
        $this->assertStringContainsString('19.99', (string) $money);
        $this->assertStringContainsString('USD', (string) $money);
    }
}

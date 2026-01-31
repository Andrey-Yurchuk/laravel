<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_can_create_valid_email(): void
    {
        $email = new Email('test@example.com');
        
        $this->assertEquals('test@example.com', $email->value());
        $this->assertEquals('test@example.com', (string) $email);
    }

    public function test_throws_exception_for_empty_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty');
        
        new Email('');
    }

    public function test_throws_exception_for_invalid_email_format(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');
        
        new Email('invalid-email');
    }

    public function test_equals_returns_true_for_same_email(): void
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('test@example.com');
        
        $this->assertTrue($email1->equals($email2));
    }

    public function test_equals_returns_false_for_different_emails(): void
    {
        $email1 = new Email('test1@example.com');
        $email2 = new Email('test2@example.com');
        
        $this->assertFalse($email1->equals($email2));
    }

    public function test_trims_whitespace(): void
    {
        $email = new Email('  test@example.com  ');
        
        $this->assertEquals('test@example.com', $email->value());
    }
}

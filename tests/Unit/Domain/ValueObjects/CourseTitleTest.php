<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\CourseTitle;
use PHPUnit\Framework\TestCase;

class CourseTitleTest extends TestCase
{
    public function test_can_create_course_title(): void
    {
        $title = new CourseTitle('Laravel Basics');
        
        $this->assertEquals('Laravel Basics', $title->value());
        $this->assertEquals('Laravel Basics', (string) $title);
    }

    public function test_throws_exception_for_empty_title(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Course title cannot be empty');
        
        new CourseTitle('');
    }

    public function test_throws_exception_for_title_exceeding_max_length(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Course title cannot exceed 255 characters');
        
        $longTitle = str_repeat('a', 256);
        new CourseTitle($longTitle);
    }

    public function test_trims_whitespace(): void
    {
        $title = new CourseTitle('  Laravel Basics  ');
        
        $this->assertEquals('Laravel Basics', $title->value());
    }

    public function test_equals_returns_true_for_same_title(): void
    {
        $title1 = new CourseTitle('Laravel Basics');
        $title2 = new CourseTitle('Laravel Basics');
        
        $this->assertTrue($title1->equals($title2));
    }
}

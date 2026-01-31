<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class CourseTitle
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmedValue = trim($value);
        
        if (empty($trimmedValue)) {
            throw new InvalidArgumentException("Course title cannot be empty");
        }
        
        if (mb_strlen($trimmedValue, 'UTF-8') > 255) {
            throw new InvalidArgumentException("Course title cannot exceed 255 characters, got: " . mb_strlen($trimmedValue, 'UTF-8'));
        }
        
        $this->value = $trimmedValue;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(CourseTitle $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

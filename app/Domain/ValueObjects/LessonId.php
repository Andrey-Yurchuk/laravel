<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class LessonId
{
    public function __construct(
        private int $value
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException("Lesson ID must be positive, got: {$value}");
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(LessonId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}

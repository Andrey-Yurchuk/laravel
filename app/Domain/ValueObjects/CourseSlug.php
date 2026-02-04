<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class CourseSlug
{
    public function __construct(
        private string $value
    ) {
        $value = trim($value);

        if (empty($value)) {
            throw new InvalidArgumentException("Course slug cannot be empty");
        }

        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            throw new InvalidArgumentException(
                "Invalid slug format: {$value}. "
                . "Slug must contain only lowercase letters, numbers, and hyphens"
            );
        }

        if (mb_strlen($value, 'UTF-8') > 255) {
            throw new InvalidArgumentException("Course slug cannot exceed 255 characters");
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(CourseSlug $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

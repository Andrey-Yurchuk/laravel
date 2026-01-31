<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class CategoryId
{
    public function __construct(
        private int $value
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException("Category ID must be positive, got: {$value}");
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(CategoryId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}

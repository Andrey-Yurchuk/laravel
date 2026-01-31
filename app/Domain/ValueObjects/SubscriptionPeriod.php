<?php

namespace App\Domain\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class SubscriptionPeriod
{
    public function __construct(
        private DateTimeImmutable $start,
        private DateTimeImmutable $end
    ) {
        if ($start >= $end) {
            throw new InvalidArgumentException(
                "Start date must be before end date. Start: {$start->format('Y-m-d H:i:s')}, End: {$end->format('Y-m-d H:i:s')}"
            );
        }
    }

    public function start(): DateTimeImmutable
    {
        return $this->start;
    }

    public function end(): DateTimeImmutable
    {
        return $this->end;
    }

    public function isActive(DateTimeImmutable $now): bool
    {
        return $now >= $this->start && $now <= $this->end;
    }

    public function daysRemaining(DateTimeImmutable $now): int
    {
        if ($now > $this->end) {
            return 0;
        }
        
        $diff = $this->end->diff($now);
        return (int) $diff->days;
    }

    public function durationInDays(): int
    {
        $diff = $this->end->diff($this->start);
        return (int) $diff->days;
    }

    public function equals(SubscriptionPeriod $other): bool
    {
        return $this->start->getTimestamp() === $other->start->getTimestamp() 
            && $this->end->getTimestamp() === $other->end->getTimestamp();
    }
}

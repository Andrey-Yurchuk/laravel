<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        private int $amount,
        private string $currency = 'USD'
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException("Amount cannot be negative, got: {$amount}");
        }

        $allowedCurrencies = ['USD', 'EUR', 'RUB'];
        if (!in_array($currency, $allowedCurrencies, true)) {
            throw new InvalidArgumentException(
                "Unsupported currency: {$currency}. "
                . "Allowed: " . implode(', ', $allowedCurrencies)
            );
        }
    }

    public static function fromDecimal(float|int $decimalAmount, string $currency = 'USD'): self
    {
        $amountInCents = (int) round($decimalAmount * 100);

        return new self($amountInCents, $currency);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    /**
     * Преобразует сумму в десятичное значение для отображения только на фронте
     */
    public function toDecimal(): float
    {
        return $this->amount / 100.0;
    }

    public function add(Money $other): Money
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Cannot add different currencies: {$this->currency} and {$other->currency}"
            );
        }

        return new Money($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): Money
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Cannot subtract different currencies: {$this->currency} and {$other->currency}"
            );
        }

        $result = $this->amount - $other->amount;
        if ($result < 0) {
            throw new InvalidArgumentException("Result cannot be negative");
        }

        return new Money($result, $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function isGreaterThan(Money $other): bool
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException("Cannot compare different currencies");
        }

        return $this->amount > $other->amount;
    }

    public function __toString(): string
    {
        return number_format($this->toDecimal(), 2, '.', '') . ' ' . $this->currency;
    }
}

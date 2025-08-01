<?php

namespace Invoice\Invoice\Domain\Primitives;

use Invoice\Base\ValueObject;

final class Amount extends ValueObject
{
    public function __construct(
        public readonly float $value,
    ) {
        if ($value < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    public static function make(float $value): self
    {
        return new self($value);
    }

    public function toValue(): float
    {
        return $this->value;
    }

    public function add(Amount $amount): self
    {
        return new self($this->value + $amount->value);
    }

    public function subtract(Amount $amount): self
    {
        return new self($this->value - $amount->value);
    }

    public function multiply(float $multiplier): self
    {
        return new self($this->value * $multiplier);
    }

    public function formatted(string $currency = 'USD'): string
    {
        return number_format($this->value, 2).' '.$currency;
    }
}

<?php

namespace Invoice\Invoice\Domain\Primitives;

use Invoice\Base\ValueObject;

final class Quantity extends ValueObject
{
    public function __construct(
        public readonly float $value,
    ) {
        if ($value < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative');
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

    public function add(Quantity $quantity): self
    {
        return new self($this->value + $quantity->value);
    }

    public function subtract(Quantity $quantity): self
    {
        return new self($this->value - $quantity->value);
    }

    public function multiply(float $multiplier): self
    {
        return new self($this->value * $multiplier);
    }

    public function formatted(): string
    {
        return number_format($this->value, 2);
    }

    /**
     * Get formatted quantity with unit
     */
    public function formattedWithUnit(string $unit = ''): string
    {
        $formatted = $this->formatted();
        return $unit ? $formatted . ' ' . $unit : $formatted;
    }

    /**
     * Convert to minutes (if quantity represents hours)
     */
    public function toMinutes(): int
    {
        return (int) ($this->value * 60);
    }

    /**
     * Create from minutes (if quantity represents hours)
     */
    public static function fromMinutes(int $minutes): self
    {
        return new self($minutes / 60);
    }
}
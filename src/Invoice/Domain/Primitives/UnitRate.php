<?php

namespace Invoice\Invoice\Domain\Primitives;

use Invoice\Base\ValueObject;

final class UnitRate extends ValueObject
{
    public function __construct(
        public readonly float $value,
    ) {
        if ($value < 0) {
            throw new \InvalidArgumentException('Unit rate cannot be negative');
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

    public function calculateAmount(Quantity $quantity): Amount
    {
        return Amount::make($this->value * $quantity->value);
    }

    public function formatted(string $currency = 'USD'): string
    {
        return number_format($this->value, 2).' '.$currency;
    }

    /**
     * Get formatted rate with unit
     */
    public function formattedWithUnit(string $unit = '', string $currency = 'USD'): string
    {
        $formatted = number_format($this->value, 2).' '.$currency;

        return $unit ? $formatted.'/'.$unit : $formatted;
    }
}

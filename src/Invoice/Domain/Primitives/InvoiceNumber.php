<?php

namespace Invoice\Invoice\Domain\Primitives;

use Invoice\Base\ValueObject;

final class InvoiceNumber extends ValueObject
{
    public function __construct(
        public readonly string $value,
    ) {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Invoice number cannot be empty');
        }
    }

    public static function make(string $value): self
    {
        return new self($value);
    }

    public function toValue(): string
    {
        return $this->value;
    }
}
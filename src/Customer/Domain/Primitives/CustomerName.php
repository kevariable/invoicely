<?php

namespace Invoice\Customer\Domain\Primitives;

use Invoice\Base\ValueObject;

final class CustomerName extends ValueObject
{
    public function __construct(
        public readonly string $value,
    ) {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Customer name cannot be empty');
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

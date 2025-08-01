<?php

namespace Invoice\Customer\Domain\Primitives;

use Invoice\Base\ValueObject;

final class Email extends ValueObject
{
    public function __construct(
        public readonly ?string $value,
    ) {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format: ' . $value);
        }
    }

    public static function make(?string $value): self
    {
        return new self($value);
    }

    public function toValue(): ?string
    {
        return $this->value;
    }
}
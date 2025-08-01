<?php

namespace Invoice\Product\Domain\Primitives;

use Invoice\Base\ValueObject;

final class ProductId extends ValueObject
{
    public function __construct(
        public readonly ?int $value,
    ) {}

    public static function make(?int $value): self
    {
        return new self($value);
    }

    public function toValue(): ?int
    {
        return $this->value;
    }
}
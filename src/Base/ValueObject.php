<?php

namespace Invoice\Base;

abstract class ValueObject
{
    /**
     * Create a new instance of the value object
     */
    abstract public static function make(mixed $value): static;

    /**
     * Get the primitive value
     */
    abstract public function toValue(): mixed;

    /**
     * Check if two value objects are equal
     */
    public function equals(ValueObject $other): bool
    {
        return $this->toValue() === $other->toValue();
    }

    /**
     * Convert to string representation
     */
    public function __toString(): string
    {
        return (string) $this->toValue();
    }
}
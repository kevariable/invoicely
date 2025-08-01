<?php

namespace Invoice\Invoice\Domain\Primitives;

use Invoice\Base\ValueObject;

final class DateTime extends ValueObject
{
    public function __construct(
        public readonly ?\DateTimeImmutable $value,
    ) {}

    public static function make(?\DateTimeImmutable $value): self
    {
        return new self($value);
    }

    public static function fromString(?string $dateString): self
    {
        if ($dateString === null) {
            return new self(null);
        }

        try {
            return new self(new \DateTimeImmutable($dateString));
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid date format: ' . $dateString, previous: $e);
        }
    }

    public static function now(): self
    {
        return new self(new \DateTimeImmutable());
    }

    public function toValue(): ?\DateTimeImmutable
    {
        return $this->value;
    }

    public function format(string $format = 'Y-m-d H:i:s'): ?string
    {
        return $this->value?->format($format);
    }

    public function isAfter(DateTime $other): bool
    {
        if ($this->value === null || $other->value === null) {
            return false;
        }

        return $this->value > $other->value;
    }

    public function isBefore(DateTime $other): bool
    {
        if ($this->value === null || $other->value === null) {
            return false;
        }

        return $this->value < $other->value;
    }
}
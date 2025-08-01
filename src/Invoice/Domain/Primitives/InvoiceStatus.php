<?php

namespace Invoice\Invoice\Domain\Primitives;

use Invoice\Base\ValueObject;

final class InvoiceStatus extends ValueObject
{
    public const DRAFT = 'draft';
    public const SENT = 'sent';
    public const PAID = 'paid';
    public const OVERDUE = 'overdue';
    public const CANCELLED = 'cancelled';

    private const VALID_STATUSES = [
        self::DRAFT,
        self::SENT,
        self::PAID,
        self::OVERDUE,
        self::CANCELLED,
    ];

    public function __construct(
        public readonly string $value,
    ) {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new \InvalidArgumentException(
                'Invalid invoice status: ' . $value . '. Valid statuses are: ' . implode(', ', self::VALID_STATUSES)
            );
        }
    }

    public static function make(string $value): self
    {
        return new self($value);
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public static function sent(): self
    {
        return new self(self::SENT);
    }

    public static function paid(): self
    {
        return new self(self::PAID);
    }

    public static function overdue(): self
    {
        return new self(self::OVERDUE);
    }

    public static function cancelled(): self
    {
        return new self(self::CANCELLED);
    }

    public function toValue(): string
    {
        return $this->value;
    }

    public function isDraft(): bool
    {
        return $this->value === self::DRAFT;
    }

    public function isSent(): bool
    {
        return $this->value === self::SENT;
    }

    public function isPaid(): bool
    {
        return $this->value === self::PAID;
    }

    public function isOverdue(): bool
    {
        return $this->value === self::OVERDUE;
    }

    public function isCancelled(): bool
    {
        return $this->value === self::CANCELLED;
    }
}
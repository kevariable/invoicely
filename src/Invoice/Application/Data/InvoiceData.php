<?php

namespace Invoice\Invoice\Application\Data;

use Invoice\Base\DataReadonly;
use Invoice\Customer\Domain\Primitives\CustomerId;
use Invoice\Invoice\Domain\Primitives\Amount;
use Invoice\Invoice\Domain\Primitives\DateTime;
use Invoice\Invoice\Domain\Primitives\InvoiceId;
use Invoice\Invoice\Domain\Primitives\InvoiceNumber;
use Invoice\Invoice\Domain\Primitives\InvoiceStatus;

final readonly class InvoiceData extends DataReadonly
{
    public function __construct(
        public InvoiceId $id,
        public InvoiceNumber $invoiceNumber,
        public CustomerId $customerId,
        public InvoiceStatus $status,
        public Amount $subtotal,
        public Amount $taxAmount,
        public Amount $totalAmount,
        public DateTime $issueDate,
        public DateTime $dueDate,
        public ?DateTime $paidDate,
        public DateTime $createdAt,
        public DateTime $updatedAt,
        /** @var \Invoice\Invoice\Application\Data\InvoiceItemData[] */
        public array $items = [],
        public ?string $notes = null,
    ) {}

    public function isPaid(): bool
    {
        return $this->status->isPaid();
    }

    public function isOverdue(): bool
    {
        if ($this->isPaid()) {
            return false;
        }

        $now = DateTime::now();

        return $this->dueDate->isBefore($now);
    }

    public function calculateTotal(): Amount
    {
        return $this->subtotal->add($this->taxAmount);
    }
}

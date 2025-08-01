<?php

namespace Invoice\Invoice\Application\Data;

use Invoice\Base\DataReadonly;
use Invoice\Invoice\Domain\Primitives\Amount;
use Invoice\Invoice\Domain\Primitives\DateTime;
use Invoice\Invoice\Domain\Primitives\Quantity;
use Invoice\Invoice\Domain\Primitives\UnitRate;
use Invoice\Invoice\Domain\Primitives\InvoiceId;

final readonly class InvoiceItemData extends DataReadonly
{
    public function __construct(
        public int $id,
        public InvoiceId $invoiceId,
        public string $description,
        public Quantity $quantity,
        public UnitRate $unitRate,
        public Amount $totalAmount,
        public DateTime $createdAt,
        public DateTime $updatedAt,
    ) {}

    public function calculateTotal(): Amount
    {
        return $this->unitRate->calculateAmount($this->quantity);
    }
}
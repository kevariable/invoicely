<?php

namespace Invoice\Invoice\Domain\Contracts;

use Invoice\Invoice\Application\Data\InvoiceData;
use Invoice\Invoice\Domain\Primitives\InvoiceId;

interface GetInvoice
{
    public function findById(InvoiceId $id): ?InvoiceData;

    public function getAllByCustomer(int $customerId): array;

    /** @return \Invoice\Invoice\Application\Data\InvoiceData[] */
    public function getAll(): array;
}
<?php

namespace Invoice\Invoice\Domain\Contracts;

use Invoice\Invoice\Application\Data\InvoiceData;
use Invoice\Invoice\Domain\Primitives\InvoiceId;

interface UpdateInvoice
{
    public function update(InvoiceId $id, InvoiceData $invoiceData): InvoiceData;
}
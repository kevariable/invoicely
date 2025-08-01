<?php

namespace Invoice\Invoice\Domain\Contracts;

use Invoice\Invoice\Application\Data\InvoiceData;

interface CreateInvoice
{
    public function create(InvoiceData $invoiceData): InvoiceData;
}

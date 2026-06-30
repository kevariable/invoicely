<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\AddInvoiceItemTool;
use App\Mcp\Tools\CreateInvoiceTool;
use App\Mcp\Tools\DuplicateInvoiceTool;
use App\Mcp\Tools\GenerateShareLinkTool;
use App\Mcp\Tools\GetInvoiceTool;
use App\Mcp\Tools\ListInvoicesTool;
use App\Mcp\Tools\MarkInvoicePaidTool;
use App\Mcp\Tools\UpdateInvoiceTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Invoicely Server')]
#[Version('0.1.0')]
#[Instructions(<<<'MARKDOWN'
    Tools for managing invoices in Invoicely.

    Typical flow: list_invoices or get_invoice to find an invoice, create_invoice
    to draft a new one for an existing customer (pass customer_id), add_invoice_item
    to append lines, update_invoice to change status/dates/notes/tax, mark_invoice_paid
    once settled, duplicate_invoice to clone as a new draft, and generate_share_link
    to get the public preview and PDF download URLs.

    Invoice numbers, subtotals and totals are computed automatically — never set them
    by hand. Amounts are in the invoice currency (default from company settings).
    MARKDOWN)]
class InvoicelyServer extends Server
{
    protected array $tools = [
        CreateInvoiceTool::class,
        GetInvoiceTool::class,
        ListInvoicesTool::class,
        UpdateInvoiceTool::class,
        AddInvoiceItemTool::class,
        MarkInvoicePaidTool::class,
        DuplicateInvoiceTool::class,
        GenerateShareLinkTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}

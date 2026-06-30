<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\PresentsInvoices;
use App\Models\Invoice;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Clone an invoice as a new draft. The copy gets a fresh invoice number, reset dates and a clean share state, and includes all line items.')]
class DuplicateInvoiceTool extends Tool
{
    use PresentsInvoices;

    protected string $name = 'duplicate_invoice';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $invoice = Invoice::find($validated['id']);

        if (! $invoice) {
            return Response::error("No invoice found with id {$validated['id']}.");
        }

        $copy = $invoice->duplicate();

        return Response::json([
            'message' => "Invoice {$invoice->invoice_number} duplicated as draft {$copy->invoice_number}.",
            'invoice' => $this->invoiceToArray($copy),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('Invoice id to duplicate.')
                ->required(),
        ];
    }
}

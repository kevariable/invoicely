<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\PresentsInvoices;
use App\Models\Invoice;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Add a line item to an existing invoice. The line total and the invoice totals are recalculated automatically.')]
class AddInvoiceItemTool extends Tool
{
    use PresentsInvoices;

    protected string $name = 'add_invoice_item';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'invoice_id' => ['required', 'integer'],
            'description' => ['required', 'string'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit_rate' => ['required', 'numeric', 'min:0'],
        ]);

        $invoice = Invoice::find($validated['invoice_id']);

        if (! $invoice) {
            return Response::error("No invoice found with id {$validated['invoice_id']}.");
        }

        $invoice->items()->create([
            'description' => $validated['description'],
            'quantity' => $validated['quantity'],
            'unit_rate' => $validated['unit_rate'],
            'total_amount' => $validated['quantity'] * $validated['unit_rate'],
        ]);

        $invoice->refresh();

        return Response::json([
            'message' => "Item added to invoice {$invoice->invoice_number}.",
            'invoice' => $this->invoiceToArray($invoice),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'invoice_id' => $schema->integer()
                ->description('Invoice id to add the item to.')
                ->required(),
            'description' => $schema->string()
                ->description('Line item description.')
                ->required(),
            'quantity' => $schema->number()
                ->description('Quantity.')
                ->min(0)
                ->required(),
            'unit_rate' => $schema->number()
                ->description('Price per unit.')
                ->min(0)
                ->required(),
        ];
    }
}

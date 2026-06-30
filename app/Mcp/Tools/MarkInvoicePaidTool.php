<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\PresentsInvoices;
use App\Models\Invoice;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Mark an invoice as paid. Sets the status to paid and records the paid date.')]
class MarkInvoicePaidTool extends Tool
{
    use PresentsInvoices;

    protected string $name = 'mark_invoice_paid';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $invoice = Invoice::find($validated['id']);

        if (! $invoice) {
            return Response::error("No invoice found with id {$validated['id']}.");
        }

        if ($invoice->isPaid()) {
            return Response::json([
                'message' => "Invoice {$invoice->invoice_number} was already paid.",
                'invoice' => $this->invoiceToArray($invoice),
            ]);
        }

        $invoice->markAsPaid();
        $invoice->refresh();

        return Response::json([
            'message' => "Invoice {$invoice->invoice_number} marked as paid.",
            'invoice' => $this->invoiceToArray($invoice),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('Invoice id to mark as paid.')
                ->required(),
        ];
    }
}

<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\PresentsInvoices;
use App\Models\Invoice;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Fetch a single invoice with its customer, line items and computed totals. Look up by id or invoice number.')]
class GetInvoiceTool extends Tool
{
    use PresentsInvoices;

    protected string $name = 'get_invoice';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['nullable', 'integer'],
            'invoice_number' => ['nullable', 'string'],
        ]);

        if (empty($validated['id']) && empty($validated['invoice_number'])) {
            return Response::error('Provide either id or invoice_number.');
        }

        $invoice = Invoice::query()
            ->when($validated['id'] ?? null, fn ($q, $id) => $q->where('id', $id))
            ->when($validated['invoice_number'] ?? null, fn ($q, $number) => $q->where('invoice_number', $number))
            ->first();

        if (! $invoice) {
            return Response::error('Invoice not found.');
        }

        return Response::json($this->invoiceToArray($invoice));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('Invoice id.'),
            'invoice_number' => $schema->string()
                ->description('Invoice number (e.g. INV-00042). Used when id is not supplied.'),
        ];
    }
}

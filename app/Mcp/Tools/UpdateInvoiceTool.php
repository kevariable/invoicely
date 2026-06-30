<?php

namespace App\Mcp\Tools;

use App\Helpers\CurrencyHelper;
use App\Mcp\Concerns\PresentsInvoices;
use App\Models\Invoice;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Carbon;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update an existing invoice. Only the fields you pass are changed. Subtotal and total are recalculated automatically.')]
class UpdateInvoiceTool extends Tool
{
    use PresentsInvoices;

    protected string $name = 'update_invoice';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'status' => ['nullable', 'in:draft,sent,paid,overdue'],
            'currency' => ['nullable', 'string', 'size:3'],
            'issue_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'capped_total_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice = Invoice::find($validated['id']);

        if (! $invoice) {
            return Response::error("No invoice found with id {$validated['id']}.");
        }

        $changes = [];

        foreach (['status', 'tax_amount', 'capped_total_amount', 'notes'] as $field) {
            if (array_key_exists($field, $validated)) {
                $changes[$field] = $validated[$field];
            }
        }

        if (array_key_exists('currency', $validated)) {
            if (! CurrencyHelper::isValidCurrency($validated['currency'])) {
                $supported = implode(', ', array_keys(CurrencyHelper::CURRENCIES));

                return Response::error("Unsupported currency '{$validated['currency']}'. Supported: {$supported}.");
            }

            $changes['currency'] = $validated['currency'];
        }

        if (isset($validated['issue_date'])) {
            $changes['issue_date'] = Carbon::parse($validated['issue_date']);
        }

        if (isset($validated['due_date'])) {
            $changes['due_date'] = Carbon::parse($validated['due_date']);
        }

        if ($changes === []) {
            return Response::error('No updatable fields were provided.');
        }

        $invoice->update($changes);
        $invoice->refresh();

        return Response::json([
            'message' => "Invoice {$invoice->invoice_number} updated.",
            'invoice' => $this->invoiceToArray($invoice),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('Invoice id to update.')
                ->required(),
            'status' => $schema->string()
                ->description('New status.')
                ->enum(['draft', 'sent', 'paid', 'overdue']),
            'currency' => $schema->string()
                ->description('3-letter currency code.'),
            'issue_date' => $schema->string()
                ->description('Issue date (YYYY-MM-DD).'),
            'due_date' => $schema->string()
                ->description('Due date (YYYY-MM-DD).'),
            'tax_amount' => $schema->number()
                ->description('Flat tax amount added to the subtotal.')
                ->min(0),
            'capped_total_amount' => $schema->number()
                ->description('Manual override that caps the total. Pass to fix the total regardless of line items.')
                ->min(0),
            'notes' => $schema->string()
                ->description('Notes shown on the invoice.'),
        ];
    }
}

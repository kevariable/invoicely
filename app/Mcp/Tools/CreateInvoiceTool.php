<?php

namespace App\Mcp\Tools;

use App\Helpers\CurrencyHelper;
use App\Mcp\Concerns\PresentsInvoices;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Carbon;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a new invoice for an existing customer, optionally with line items. The invoice number, subtotal and total are generated automatically.')]
class CreateInvoiceTool extends Tool
{
    use PresentsInvoices;

    protected string $name = 'create_invoice';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer'],
            'status' => ['nullable', 'in:draft,sent,paid,overdue'],
            'currency' => ['nullable', 'string', 'size:3'],
            'issue_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.description' => ['required_with:items', 'string'],
            'items.*.quantity' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.unit_rate' => ['required_with:items', 'numeric', 'min:0'],
        ]);

        $customer = Customer::find($validated['customer_id']);

        if (! $customer) {
            return Response::error("No customer found with id {$validated['customer_id']}.");
        }

        $currency = $validated['currency'] ?? CurrencyHelper::getDefaultCurrency();

        if (! CurrencyHelper::isValidCurrency($currency)) {
            $supported = implode(', ', array_keys(CurrencyHelper::CURRENCIES));

            return Response::error("Unsupported currency '{$currency}'. Supported: {$supported}.");
        }

        $issueDate = isset($validated['issue_date']) ? Carbon::parse($validated['issue_date']) : Carbon::now();
        $dueDate = isset($validated['due_date']) ? Carbon::parse($validated['due_date']) : (clone $issueDate)->addDays(30);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'customer_id' => $customer->id,
            'status' => $validated['status'] ?? 'draft',
            'currency' => $currency,
            'tax_amount' => $validated['tax_amount'] ?? 0,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] ?? [] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_rate' => $item['unit_rate'],
                'total_amount' => $item['quantity'] * $item['unit_rate'],
            ]);
        }

        $invoice->refresh();

        return Response::json([
            'message' => "Invoice {$invoice->invoice_number} created.",
            'invoice' => $this->invoiceToArray($invoice),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'customer_id' => $schema->integer()
                ->description('ID of an existing customer to bill.')
                ->required(),
            'status' => $schema->string()
                ->description('Invoice status. Defaults to draft.')
                ->enum(['draft', 'sent', 'paid', 'overdue']),
            'currency' => $schema->string()
                ->description('3-letter currency code (e.g. USD, GBP, IDR, MYR). Defaults to the company default.'),
            'issue_date' => $schema->string()
                ->description('Issue date (YYYY-MM-DD). Defaults to today.'),
            'due_date' => $schema->string()
                ->description('Due date (YYYY-MM-DD). Defaults to 30 days after the issue date.'),
            'tax_amount' => $schema->number()
                ->description('Flat tax amount added to the subtotal. Defaults to 0.')
                ->min(0),
            'notes' => $schema->string()
                ->description('Optional notes shown on the invoice.'),
            'items' => $schema->array()
                ->description('Optional line items to add to the invoice.')
                ->items($schema->object([
                    'description' => $schema->string()->required(),
                    'quantity' => $schema->number()->min(0)->required(),
                    'unit_rate' => $schema->number()->min(0)->required(),
                ])),
        ];
    }
}

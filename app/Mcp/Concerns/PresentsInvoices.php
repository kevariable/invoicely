<?php

namespace App\Mcp\Concerns;

use App\Models\Invoice;

trait PresentsInvoices
{
    protected function invoiceToArray(Invoice $invoice): array
    {
        $invoice->loadMissing(['customer', 'items']);

        return [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'status' => $invoice->status,
            'view_state' => $invoice->view_state,
            'currency' => $invoice->currency,
            'customer' => $invoice->customer ? [
                'id' => $invoice->customer->id,
                'name' => $invoice->customer->name,
                'email' => $invoice->customer->email,
            ] : null,
            'issue_date' => optional($invoice->issue_date)->toDateString(),
            'due_date' => optional($invoice->due_date)->toDateString(),
            'paid_date' => optional($invoice->paid_date)->toDateTimeString(),
            'subtotal' => (float) $invoice->subtotal,
            'tax_amount' => (float) $invoice->tax_amount,
            'capped_total_amount' => $invoice->capped_total_amount !== null ? (float) $invoice->capped_total_amount : null,
            'total_amount' => (float) $invoice->total_amount,
            'formatted_total' => $invoice->getFormattedTotalAmount(),
            'notes' => $invoice->notes,
            'public_url' => $invoice->public_token ? url("/invoice/preview/{$invoice->public_token}") : null,
            'items' => $invoice->items->map(fn ($item) => [
                'id' => $item->id,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_rate' => (float) $item->unit_rate,
                'total_amount' => (float) $item->total_amount,
                'sort' => $item->sort,
            ])->all(),
        ];
    }
}

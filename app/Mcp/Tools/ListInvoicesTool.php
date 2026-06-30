<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\PresentsInvoices;
use App\Models\Invoice;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('List invoices, newest first, with optional filtering by status, view state or customer. Returns a paginated summary.')]
class ListInvoicesTool extends Tool
{
    use PresentsInvoices;

    protected string $name = 'list_invoices';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:draft,sent,paid,overdue'],
            'view_state' => ['nullable', 'in:unread,viewed'],
            'customer_id' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $limit = $validated['limit'] ?? 25;

        $paginator = Invoice::query()
            ->when($validated['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($validated['view_state'] ?? null, fn ($q, $state) => $q->where('view_state', $state))
            ->when($validated['customer_id'] ?? null, fn ($q, $id) => $q->where('customer_id', $id))
            ->with(['customer', 'items'])
            ->orderByDesc('id')
            ->paginate(perPage: $limit, page: $validated['page'] ?? 1);

        return Response::json([
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'invoices' => collect($paginator->items())
                ->map(fn (Invoice $invoice) => $this->invoiceToArray($invoice))
                ->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()
                ->description('Filter by status.')
                ->enum(['draft', 'sent', 'paid', 'overdue']),
            'view_state' => $schema->string()
                ->description('Filter by whether the share link has been opened.')
                ->enum(['unread', 'viewed']),
            'customer_id' => $schema->integer()
                ->description('Filter by customer id.'),
            'limit' => $schema->integer()
                ->description('Results per page (1-100). Defaults to 25.')
                ->min(1)
                ->max(100),
            'page' => $schema->integer()
                ->description('Page number. Defaults to 1.')
                ->min(1),
        ];
    }
}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - {{ $companySettings->person_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            color: #111827;
            background-color: #ffffff;
        }
        
        .page {
            width: 210mm;
            height: 297mm;
            padding: 10mm;
            box-sizing: border-box;
        }

        /* Ensure proper page breaks */
        .page-break {
            page-break-before: always;
        }
        
        /* Print-specific styles */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">INVOICE</h1>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' :
                           ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                    @if($invoice->isViewed())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Viewed {{ $invoice->viewed_at->format('M j, Y') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-gray-900">{{ $invoice->invoice_number }}</div>
                <div class="text-sm text-gray-600 mt-1">
                    Issue Date: {{ $invoice->issue_date->format('M j, Y') }}
                </div>
                <div class="text-sm text-gray-600">
                    Due Date: {{ $invoice->due_date->format('M j, Y') }}
                </div>
            </div>
        </div>

        <!-- From/To Section -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <!-- From -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">From</h3>
                <div class="text-gray-900">
                    <div class="mt-2 text-sm text-gray-600 space-y-1">
                        <div>{{ $companySettings->address }}</div>
                        <div>
                            {{ $companySettings->city }}@if($companySettings->city && $companySettings->state), @endif{{ $companySettings->state }} {{ $companySettings->zip_code }}
                        </div>
                        @if($companySettings->country)
                            <div>{{ $companySettings->country }}</div>
                        @endif
                    </div>
                    @if($companySettings->phone || $companySettings->email)
                        <div class="mt-3 text-sm text-gray-600 space-y-1">
                            @if($companySettings->phone)
                                <div>{{ $companySettings->phone }}</div>
                            @endif
                            @if($companySettings->email)
                                <div>{{ $companySettings->email }}</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- To -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Bill To</h3>
                <div class="text-gray-900">
                    <div class="mt-2 text-sm text-gray-600 space-y-1">
                        <div>{{ $invoice->customer->name }}</div>
                        <div>{{ $invoice->customer->address }}</div>
                        <div>
                            {{ $invoice->customer->city }}@if($invoice->customer->city && $invoice->customer->state), @endif{{ $invoice->customer->state }} {{ $invoice->customer->zip_code }}
                        </div>
                        @if($invoice->customer->country)
                            <div>{{ $invoice->customer->country }}</div>
                        @endif
                    </div>
                    @if($invoice->customer->phone)
                        <div class="mt-2 text-sm text-gray-600">{{ $invoice->customer->phone }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-8">
            <div class="overflow-hidden border rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Description
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Quantity
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Rate
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Amount
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $item->description }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                    {{ number_format($item->quantity, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                    {{ \App\Helpers\CurrencyHelper::format($item->unit_rate, $invoice->currency) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">
                                    {{ \App\Helpers\CurrencyHelper::format($item->total_amount, $invoice->currency) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals -->
        <div class="flex justify-end">
            <div class="w-64">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-gray-900">{{ $invoice->getFormattedSubtotal() }}</span>
                        </div>
                        @if($invoice->tax_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax:</span>
                                <span class="text-gray-900">{{ $invoice->getFormattedTaxAmount() }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-gray-900">{{ $invoice->getFormattedTotalAmount() }}</span>
                            </div>
                        </div>
                        @if($invoice->status !== 'paid')
                            <div class="flex justify-between text-sm font-medium">
                                <span class="text-red-600">Balance Due:</span>
                                <span class="text-red-600">{{ $invoice->getFormattedTotalAmount() }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($invoice->notes)
            <div class="mt-8">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-2">Notes</h3>
                <div class="bg-gray-50 p-4 rounded-lg whitespace-pre-wrap">{{ $invoice->notes }}</div>
            </div>
        @endif

        <!-- Footer -->
        <div class="mt-12 py-8 border-t border-gray-200">
            <div class="text-center text-sm text-gray-600">
                @if($companySettings->email || $companySettings->website)
                    <div class="mb-2">
                        @if($companySettings->email){{ $companySettings->email }}@endif
                        @if($companySettings->email && $companySettings->website) | @endif
                        @if($companySettings->website){{ $companySettings->website }}@endif
                    </div>
                @endif
                <div class="font-medium">Thank you for your business!</div>
            </div>
        </div>
    </div>
</body>
</html> 
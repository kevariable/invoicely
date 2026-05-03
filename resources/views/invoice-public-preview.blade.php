<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - {{ $companySettings->company_name }}</title>
    <link rel="icon" type="image/gif" href="{{ asset('favicon.gif') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Header Actions -->
    <div class="no-print bg-white shadow-sm border-b sticky top-0 z-10">
        <div class="max-w-4xl mx-auto px-4 py-3">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">Invoice Preview</h1>
                    <p class="text-sm text-gray-600">{{ $invoice->invoice_number }}</p>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                    <a href="{{ route('invoice.public.download', $invoice->public_token) }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Content -->
    <div class="max-w-4xl mx-auto p-4 sm:p-6">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-4 sm:p-8">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-8">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">INVOICE</h1>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                   ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                            @if($invoice->isViewed() && $invoice->viewed_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Viewed {{ $invoice->viewed_at->format('M j, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <div class="text-xl sm:text-2xl font-bold text-gray-900">{{ $invoice->invoice_number }}</div>
                        <div class="text-sm text-gray-600 mt-1">
                            Issue Date: {{ $invoice->issue_date->format('M j, Y') }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Due Date: {{ $invoice->due_date->format('M j, Y') }}
                        </div>
                    </div>
                </div>

                <!-- From/To Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mb-8">
                    <!-- From -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">From</h3>
                        <div class="text-gray-900">
                            <div class="font-semibold text-base mb-2">{{ $companySettings->person_name }}</div>
                            <div class="text-sm text-gray-600 space-y-1">
                                @if($companySettings->address)
                                    <div>{{ $companySettings->address }}</div>
                                @endif
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
                            <div class="font-semibold text-base mb-2">{{ $invoice->customer->name }}</div>
                            <div class="text-sm text-gray-600 space-y-1">
                                @if($invoice->customer->address)
                                    <div>{{ $invoice->customer->address }}</div>
                                @endif
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

                <!-- Items Section Header -->
                <div class="lg:hidden mb-6">
                    <div class="bg-gray-100 text-gray-700 font-semibold text-sm uppercase tracking-wide px-4 py-3 rounded-lg text-center">
                        Items
                    </div>
                </div>

                <!-- Items Table - Desktop -->
                <div class="hidden lg:block mb-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
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

                <!-- Items Table - Mobile -->
                <div class="lg:hidden mb-8">
                    @foreach($invoice->items as $item)
                        <div class="border border-gray-200 rounded-lg p-5 mb-4 bg-white shadow-sm">
                            <div class="font-semibold text-gray-900 text-base mb-4 pb-3 border-b border-gray-100">
                                {{ $item->description }}
                            </div>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Quantity</span>
                                    <span class="text-sm font-medium text-gray-900 pt-1">{{ number_format($item->quantity, 2) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Rate/Unit</span>
                                    <span class="text-sm font-medium text-gray-900 pt-1">{{ \App\Helpers\CurrencyHelper::format($item->unit_rate, $invoice->currency) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</span>
                                    <span class="text-sm font-medium text-gray-900 pt-1">{{ \App\Helpers\CurrencyHelper::format($item->total_amount, $invoice->currency) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="flex justify-end">
                    <div class="w-full sm:w-64">
                        <div class="bg-gray-50 p-4 sm:p-6 rounded-lg">
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
                                @if($invoice->isCapped())
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Adjustment:</span>
                                        <span class="text-gray-900">{{ $invoice->getFormattedCapAdjustment() }}</span>
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
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Notes</h3>
                        <div class="text-sm text-gray-600 whitespace-pre-wrap">{{ $invoice->notes }}</div>
                    </div>
                @endif

                <!-- Footer -->
                <div class="mt-8 pt-8 border-t border-gray-200 text-center">
                    <div class="text-sm text-gray-600 space-y-1">
                        @if($companySettings->email || $companySettings->website)
                            <div>
                                @if($companySettings->email)
                                    <a href="mailto:{{ $companySettings->email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $companySettings->email }}
                                    </a>
                                @endif
                                @if($companySettings->email && $companySettings->website) | @endif
                                @if($companySettings->website)
                                    <a href="{{ $companySettings->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        {{ $companySettings->website }}
                                    </a>
                                @endif
                            </div>
                        @endif
                        <div class="text-xs text-gray-500 mt-2">
                            Thank you for your business!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="no-print text-center py-8">
        <p class="text-sm text-gray-500">
            This is a secure invoice preview. 
            @if($companySettings->company_name)
                Generated by {{ $companySettings->company_name }}.
            @endif
        </p>
    </div>
</body>
</html>
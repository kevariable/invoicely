<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        },
                    },
                },
            },
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page { size: A4; margin: 0; }
        html, body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="mx-auto w-full max-w-4xl px-4 py-6 sm:px-6 sm:py-10">
        <article class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">

            {{-- Brand bar --}}
            <div class="h-2 w-full bg-gradient-to-r from-brand-500 via-brand-600 to-brand-700"></div>

            <div class="px-6 py-8 sm:px-10 sm:py-10">

                {{-- Header --}}
                <header class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand-600">Invoice</p>
                        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                            {{ $invoice->invoice_number }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-2">
                            @php
                                $statusStyles = [
                                    'paid'    => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                    'overdue' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                                    'sent'    => 'bg-amber-50 text-amber-800 ring-amber-600/20',
                                    'draft'   => 'bg-slate-100 text-slate-700 ring-slate-500/20',
                                ];
                                $statusClass = $statusStyles[$invoice->status] ?? 'bg-slate-100 text-slate-700 ring-slate-500/20';
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                            @if($invoice->isViewed() && $invoice->viewed_at)
                                <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-600/20">
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM10 15a5 5 0 100-10 5 5 0 000 10z" clip-rule="evenodd"/>
                                    </svg>
                                    Viewed {{ $invoice->viewed_at->format('M j, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm sm:text-right">
                        <dt class="text-slate-500 sm:order-1">Issue date</dt>
                        <dd class="font-medium text-slate-900 sm:order-2">{{ $invoice->issue_date->format('M j, Y') }}</dd>
                        <dt class="text-slate-500 sm:order-3">Due date</dt>
                        <dd class="font-medium text-slate-900 sm:order-4">{{ $invoice->due_date->format('M j, Y') }}</dd>
                    </dl>
                </header>

                <div class="my-8 h-px bg-slate-200"></div>

                {{-- From / Bill To --}}
                <section class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">From</h2>
                        <p class="mt-3 text-base font-semibold text-slate-900">{{ $companySettings->person_name }}</p>
                        <address class="mt-2 space-y-0.5 text-sm not-italic leading-relaxed text-slate-600">
                            @if($companySettings->address)
                                <div>{{ $companySettings->address }}</div>
                            @endif
                            <div>
                                {{ $companySettings->city }}@if($companySettings->city && $companySettings->state), @endif{{ $companySettings->state }} {{ $companySettings->zip_code }}
                            </div>
                            @if($companySettings->country)
                                <div>{{ $companySettings->country }}</div>
                            @endif
                        </address>
                        @if($companySettings->phone || $companySettings->email)
                            <div class="mt-3 space-y-0.5 text-sm leading-relaxed text-slate-600">
                                @if($companySettings->phone)
                                    <div>{{ $companySettings->phone }}</div>
                                @endif
                                @if($companySettings->email)
                                    <div class="break-all">{{ $companySettings->email }}</div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="sm:text-left">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Bill to</h2>
                        <p class="mt-3 text-base font-semibold text-slate-900">{{ $invoice->customer->name }}</p>
                        <address class="mt-2 space-y-0.5 text-sm not-italic leading-relaxed text-slate-600">
                            @if($invoice->customer->address)
                                <div>{{ $invoice->customer->address }}</div>
                            @endif
                            <div>
                                {{ $invoice->customer->city }}@if($invoice->customer->city && $invoice->customer->state), @endif{{ $invoice->customer->state }} {{ $invoice->customer->zip_code }}
                            </div>
                            @if($invoice->customer->country)
                                <div>{{ $invoice->customer->country }}</div>
                            @endif
                        </address>
                        @if($invoice->customer->phone)
                            <div class="mt-3 text-sm leading-relaxed text-slate-600">{{ $invoice->customer->phone }}</div>
                        @endif
                    </div>
                </section>

                {{-- Items: table on >=sm, card list on <sm --}}
                <section class="mt-10">
                    {{-- Desktop / PDF table --}}
                    <div class="hidden overflow-hidden rounded-xl ring-1 ring-slate-200 sm:block">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Description</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Qty</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Rate</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($invoice->items as $item)
                                    <tr>
                                        <td class="whitespace-pre-line px-6 py-4 text-sm text-slate-900">{{ $item->description }}</td>
                                        <td class="px-6 py-4 text-right text-sm tabular-nums text-slate-700">{{ number_format($item->quantity, 2) }}</td>
                                        <td class="px-6 py-4 text-right text-sm tabular-nums text-slate-700">{{ \App\Helpers\CurrencyHelper::format($item->unit_rate, $invoice->currency) }}</td>
                                        <td class="px-6 py-4 text-right text-sm font-semibold tabular-nums text-slate-900">{{ \App\Helpers\CurrencyHelper::format($item->total_amount, $invoice->currency) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile card list --}}
                    <ul class="space-y-3 sm:hidden">
                        @foreach($invoice->items as $item)
                            <li class="rounded-xl bg-white p-4 ring-1 ring-slate-200">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->description }}</p>
                                <dl class="mt-3 grid grid-cols-3 gap-2 text-xs">
                                    <div>
                                        <dt class="font-medium uppercase tracking-wide text-slate-500">Qty</dt>
                                        <dd class="mt-1 tabular-nums text-slate-900">{{ number_format($item->quantity, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium uppercase tracking-wide text-slate-500">Rate</dt>
                                        <dd class="mt-1 tabular-nums text-slate-900">{{ \App\Helpers\CurrencyHelper::format($item->unit_rate, $invoice->currency) }}</dd>
                                    </div>
                                    <div class="text-right">
                                        <dt class="font-medium uppercase tracking-wide text-slate-500">Amount</dt>
                                        <dd class="mt-1 font-semibold tabular-nums text-slate-900">{{ \App\Helpers\CurrencyHelper::format($item->total_amount, $invoice->currency) }}</dd>
                                    </div>
                                </dl>
                            </li>
                        @endforeach
                    </ul>
                </section>

                {{-- Totals --}}
                <section class="mt-8 flex justify-end">
                    <div class="w-full rounded-xl bg-slate-50 p-6 ring-1 ring-slate-200 sm:w-80">
                        <dl class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600">Subtotal</dt>
                                <dd class="tabular-nums text-slate-900">{{ $invoice->getFormattedSubtotal() }}</dd>
                            </div>
                            @if($invoice->tax_amount > 0)
                                <div class="flex items-center justify-between">
                                    <dt class="text-slate-600">Tax</dt>
                                    <dd class="tabular-nums text-slate-900">{{ $invoice->getFormattedTaxAmount() }}</dd>
                                </div>
                            @endif
                            <div class="flex items-center justify-between border-t border-slate-200 pt-3 text-base font-bold">
                                <dt class="text-slate-900">Total</dt>
                                <dd class="tabular-nums text-slate-900">{{ $invoice->getFormattedTotalAmount() }}</dd>
                            </div>
                            @if($invoice->status !== 'paid')
                                <div class="mt-1 flex items-center justify-between rounded-lg bg-rose-50 px-3 py-2 text-sm font-semibold ring-1 ring-rose-200">
                                    <dt class="text-rose-700">Balance due</dt>
                                    <dd class="tabular-nums text-rose-700">{{ $invoice->getFormattedTotalAmount() }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </section>

                {{-- Notes --}}
                @if($invoice->notes)
                    <section class="mt-10 border-t border-slate-200 pt-6">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Notes</h2>
                        <p class="mt-3 whitespace-pre-wrap text-sm leading-relaxed text-slate-600">{{ $invoice->notes }}</p>
                    </section>
                @endif

                {{-- Footer --}}
                <footer class="mt-10 border-t border-slate-200 pt-6 text-center">
                    @if($companySettings->email || $companySettings->website)
                        <p class="text-sm text-slate-600">
                            @if($companySettings->email)<span class="break-all">{{ $companySettings->email }}</span>@endif
                            @if($companySettings->email && $companySettings->website) <span class="mx-1 text-slate-300">•</span> @endif
                            @if($companySettings->website)<span class="break-all">{{ $companySettings->website }}</span>@endif
                        </p>
                    @endif
                    <p class="mt-2 text-xs text-slate-500">Thank you for your business.</p>
                </footer>
            </div>
        </article>
    </div>
</body>
</html>

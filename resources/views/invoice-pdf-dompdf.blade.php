<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_number }} - {{ $companySettings->person_name }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        
        body {
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            background-color: #ffffff;
            color: #111827;
            line-height: 1.5;
            font-size: 14px;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 16px;
        }
        
        .invoice-card {
            background: white;
            border: 1px solid #e5e7eb;
            padding: 24px;
        }
        
        /* Header */
        .header {
            width: 100%;
            margin-bottom: 24px;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        
        .header-left h1 {
            font-size: 30px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-right: 6px;
            margin-bottom: 4px;
        }
        
        .badge-paid { 
            background-color: #dcfce7; 
            color: #166534; 
        }
        
        .badge-overdue { 
            background-color: #fee2e2; 
            color: #991b1b; 
        }
        
        .badge-pending { 
            background-color: #fef3c7; 
            color: #92400e; 
        }
        
        .badge-viewed {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .invoice-number {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        
        .invoice-dates {
            font-size: 12px;
            color: #6b7280;
        }
        
        /* From/To Section */
        .address-section {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }
        
        .from-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .to-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }
        
        .address-content {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.4;
        }
        
        .address-content div {
            margin-bottom: 2px;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }
        
        .items-table th {
            background-color: #f9fafb;
            padding: 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table td {
            padding: 12px;
            font-size: 12px;
            color: #111827;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .items-table td.text-right {
            text-align: right;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        /* Totals */
        .totals-section {
            display: table;
            width: 100%;
        }
        
        .totals-spacer {
            display: table-cell;
            width: 60%;
        }
        
        .totals-content {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }
        
        .totals-box {
            background-color: #f9fafb;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .total-row:last-child {
            margin-bottom: 0;
        }
        
        .total-label {
            display: table-cell;
            width: 50%;
            font-size: 12px;
            color: #6b7280;
        }
        
        .total-value {
            display: table-cell;
            width: 50%;
            font-size: 12px;
            color: #111827;
            text-align: right;
            font-weight: 500;
        }
        
        .total-row.grand-total {
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            margin-top: 8px;
        }
        
        .total-row.grand-total .total-label,
        .total-row.grand-total .total-value {
            font-size: 14px;
            font-weight: 700;
        }
        
        .total-row.balance-due .total-label,
        .total-row.balance-due .total-value {
            color: #dc2626;
            font-weight: 600;
        }
        
        /* Notes */
        .notes-section {
            margin-top: 24px;
        }
        
        .notes-title {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        
        .notes-content {
            background-color: #f9fafb;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            font-size: 12px;
            color: #374151;
            white-space: pre-wrap;
        }
        
        /* Footer */
        .footer {
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        
        .footer-content {
            font-size: 11px;
            color: #6b7280;
        }
        
        .footer-content div {
            margin-bottom: 4px;
        }
        
        .footer-content .thank-you {
            font-weight: 600;
            color: #374151;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-card">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <div class="header-left">
                        <h1>INVOICE</h1>
                        <div>
                            <span class="badge badge-{{ $invoice->status }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                            @if($invoice->isViewed())
                                <span class="badge badge-viewed">
                                    Viewed {{ $invoice->viewed_at->format('M j, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="header-right">
                        <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                        <div class="invoice-dates">
                            <div>Issue Date: {{ $invoice->issue_date->format('M j, Y') }}</div>
                            <div>Due Date: {{ $invoice->due_date->format('M j, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- From/To Section -->
            <div class="address-section">
                <div class="from-section">
                    <div class="section-title">From</div>
                    <div class="address-content">
                        <div>{{ $companySettings->address }}</div>
                        <div>
                            {{ $companySettings->city }}@if($companySettings->city && $companySettings->state), @endif{{ $companySettings->state }} {{ $companySettings->zip_code }}
                        </div>
                        @if($companySettings->country)
                            <div>{{ $companySettings->country }}</div>
                        @endif
                        @if($companySettings->phone || $companySettings->email)
                            <div style="margin-top: 8px;">
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

                <div class="to-section">
                    <div class="section-title">Bill To</div>
                    <div class="address-content">
                        <div>{{ $invoice->customer->name }}</div>
                        <div>{{ $invoice->customer->address }}</div>
                        <div>
                            {{ $invoice->customer->city }}@if($invoice->customer->city && $invoice->customer->state), @endif{{ $invoice->customer->state }} {{ $invoice->customer->zip_code }}
                        </div>
                        @if($invoice->customer->country)
                            <div>{{ $invoice->customer->country }}</div>
                        @endif
                        @if($invoice->customer->phone)
                            <div style="margin-top: 4px;">{{ $invoice->customer->phone }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Rate</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $item->description }}</td>
                            <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-right">{{ \App\Helpers\CurrencyHelper::format($item->unit_rate, $invoice->currency) }}</td>
                            <td class="text-right">{{ \App\Helpers\CurrencyHelper::format($item->total_amount, $invoice->currency) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals -->
            <div class="totals-section">
                <div class="totals-spacer"></div>
                <div class="totals-content">
                    <div class="totals-box">
                        <div class="total-row">
                            <div class="total-label">Subtotal:</div>
                            <div class="total-value">{{ $invoice->getFormattedSubtotal() }}</div>
                        </div>
                        @if($invoice->tax_amount > 0)
                            <div class="total-row">
                                <div class="total-label">Tax:</div>
                                <div class="total-value">{{ $invoice->getFormattedTaxAmount() }}</div>
                            </div>
                        @endif
                        <div class="total-row grand-total">
                            <div class="total-label">Total:</div>
                            <div class="total-value">{{ $invoice->getFormattedTotalAmount() }}</div>
                        </div>
                        @if($invoice->status !== 'paid')
                            <div class="total-row balance-due">
                                <div class="total-label">Balance Due:</div>
                                <div class="total-value">{{ $invoice->getFormattedTotalAmount() }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($invoice->notes)
                <div class="notes-section">
                    <div class="notes-title">Notes</div>
                    <div class="notes-content">{{ $invoice->notes }}</div>
                </div>
            @endif

            <!-- Footer -->
            <div class="footer">
                <div class="footer-content">
                    @if($companySettings->email || $companySettings->website)
                        <div>
                            @if($companySettings->email){{ $companySettings->email }}@endif
                            @if($companySettings->email && $companySettings->website) | @endif
                            @if($companySettings->website){{ $companySettings->website }}@endif
                        </div>
                    @endif
                    <div class="thank-you">Thank you for your business!</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 
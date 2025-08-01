<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_number }} - {{ $companySettings->company_name }}</title>
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
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
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
            color: #dc2626; 
        }
        .badge-default { 
            background-color: #fef3c7; 
            color: #d97706; 
        }
        .badge-viewed { 
            background-color: #dbeafe; 
            color: #1d4ed8; 
        }
        
        .invoice-number {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        
        .dates {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.3;
        }
        
        /* From/To Section */
        .from-to-section {
            width: 100%;
            margin-bottom: 24px;
        }
        
        .from-to-content {
            display: table;
            width: 100%;
        }
        
        .from-section, .to-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 16px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 6px;
        }
        
        .address, .contact-info {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.4;
        }
        
        .contact-info {
            margin-top: 8px;
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
            padding: 12px 24px;
            text-align: left;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table td {
            padding: 16px 24px;
            font-size: 14px;
            color: #111827;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .items-table td.text-right {
            text-align: right;
        }
        
        .items-table td.amount {
            font-weight: 500;
        }
        
        /* Totals Section */
        .totals-section {
            width: 100%;
            margin-bottom: 24px;
        }
        
        .totals-container {
            display: table;
            width: 100%;
        }
        
        .totals-spacer {
            display: table-cell;
            width: 60%;
        }
        
        .totals {
            display: table-cell;
            width: 40%;
            background-color: #f9fafb;
            padding: 16px;
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
            text-align: left;
        }
        
        .total-amount {
            display: table-cell;
            text-align: right;
        }
        
        .total-row.subtotal,
        .total-row.tax {
            font-size: 12px;
            color: #6b7280;
        }
        
        .total-row.grand-total {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            margin-top: 12px;
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }
        
        .total-row.balance-due {
            font-size: 14px;
            font-weight: 500;
            color: #dc2626;
        }
        
        /* Notes Section */
        .notes-section {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }
        
        .notes-title {
            font-size: 11px;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .notes-content {
            font-size: 12px;
            color: #6b7280;
            white-space: pre-wrap;
        }
        
        /* Footer */
        .footer {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        
        .footer-links {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 6px;
        }
        
        .footer-note {
            font-size: 11px;
            color: #9ca3af;
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
                            <span class="badge {{ $invoice->status === 'paid' ? 'badge-paid' : ($invoice->status === 'overdue' ? 'badge-overdue' : 'badge-default') }}">
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
                        <div class="dates">
                            Issue Date: {{ $invoice->issue_date->format('M j, Y') }}<br>
                            Due Date: {{ $invoice->due_date->format('M j, Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- From/To Section -->
            <div class="from-to-section">
                <div class="from-to-content">
                    <div class="from-section">
                        <div class="section-title">From</div>
                        <div class="company-name">{{ $companySettings->company_name }}</div>
                        @if($companySettings->address)
                            <div class="address">
                                {{ $companySettings->address }}<br>
                                {{ $companySettings->city }}@if($companySettings->city && $companySettings->state), @endif{{ $companySettings->state }} {{ $companySettings->zip_code }}<br>
                                @if($companySettings->country){{ $companySettings->country }}<br>@endif
                            </div>
                        @endif
                        @if($companySettings->phone || $companySettings->email)
                            <div class="contact-info">
                                @if($companySettings->phone){{ $companySettings->phone }}<br>@endif
                                @if($companySettings->email){{ $companySettings->email }}@endif
                            </div>
                        @endif
                    </div>

                    <div class="to-section">
                        <div class="section-title">Bill To</div>
                        <div class="address">
                            {{ $invoice->customer->name }}
                            {{ $invoice->customer->address }}<br>
                            {{ $invoice->customer->city }}@if($invoice->customer->city && $invoice->customer->state), @endif{{ $invoice->customer->state }} {{ $invoice->customer->zip_code }}<br>
                            @if($invoice->customer->country){{ $invoice->customer->country }}<br>@endif
                        </div>
                        @if($invoice->customer->phone)
                            <div class="contact-info">{{ $invoice->customer->phone }}</div>
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
                            <td class="text-right amount">{{ \App\Helpers\CurrencyHelper::format($item->total_amount, $invoice->currency) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals -->
            <div class="totals-section">
                <div class="totals-container">
                    <div class="totals-spacer"></div>
                    <div class="totals">
                        <div class="total-row subtotal">
                            <div class="total-label">Subtotal:</div>
                            <div class="total-amount">{{ $invoice->getFormattedSubtotal() }}</div>
                        </div>
                        @if($invoice->tax_amount > 0)
                            <div class="total-row tax">
                                <div class="total-label">Tax:</div>
                                <div class="total-amount">{{ $invoice->getFormattedTaxAmount() }}</div>
                            </div>
                        @endif
                        <div class="total-row grand-total">
                            <div class="total-label">Total:</div>
                            <div class="total-amount">{{ $invoice->getFormattedTotalAmount() }}</div>
                        </div>
                        @if($invoice->status !== 'paid')
                            <div class="total-row balance-due">
                                <div class="total-label">Balance Due:</div>
                                <div class="total-amount">{{ $invoice->getFormattedTotalAmount() }}</div>
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
                @if($companySettings->email || $companySettings->website)
                    <div class="footer-links">
                        @if($companySettings->email){{ $companySettings->email }}@endif
                        @if($companySettings->email && $companySettings->website) | @endif
                        @if($companySettings->website){{ $companySettings->website }}@endif
                    </div>
                @endif
                <div class="footer-note">
                    Thank you for your business!
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_number }} - {{ $companySettings->person_name }}</title>
    <style>
        @page {
            margin: 10mm;
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
            line-height: 1.5;
            color: #111827;
            background-color: #f9fafb; /* Match preview background */
            font-size: 14px; /* Increased base font size */
        }

        .page {
            width: 100%;
            max-width: 190mm;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 32px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Header */
        .header {
            width: 100%;
            margin-bottom: 32px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 50%;
            vertical-align: top;
        }

        .header-right {
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .header-left h1 {
            font-size: 32px; /* Increased from 24px to match preview */
            font-weight: 700;
            color: #111827;
            margin-bottom: 12px;
            letter-spacing: -0.025em;
        }

        .header-badges {
            margin-bottom: 8px;
        }

        .header-badges .badge {
            display: inline-block;
            margin-right: 8px;
            margin-bottom: 4px;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px; /* Increased padding */
            border-radius: 16px; /* More rounded */
            font-size: 12px; /* Increased font size */
            font-weight: 600; /* Changed from bold to 600 */
            margin-right: 8px;
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
            font-size: 24px; /* Increased from 20px */
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .invoice-dates {
            font-size: 14px; /* Increased from 12px */
            color: #6b7280;
            line-height: 1.4;
        }

        .invoice-dates div {
            margin-bottom: 2px;
        }

        /* From/To Section */
        .address-section {
            width: 100%;
            margin-bottom: 32px;
        }

        .address-table {
            width: 100%;
            border-collapse: collapse;
        }

        .address-table td {
            width: 50%;
            vertical-align: top;
            padding-right: 32px; /* Increased from 16px */
        }

        .section-title {
            font-size: 14px; /* Increased from 12px */
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }

        .address-content {
            color: #111827;
            font-size: 14px; /* Added base font size */
        }

        .address-content .company-name {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 8px;
        }

        .address-content .address-details {
            margin-top: 8px;
            font-size: 14px; /* Increased from 12px */
            color: #6b7280;
            line-height: 1.4;
        }

        .address-content .address-details div {
            margin-bottom: 4px;
        }

        .address-content .contact-info {
            margin-top: 12px;
            font-size: 14px; /* Increased from 12px */
            color: #6b7280;
        }

        .address-content .contact-info div {
            margin-bottom: 4px;
        }

        /* Items Table */
        .items-container {
            margin-bottom: 32px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .items-table th {
            background-color: #f9fafb;
            padding: 12px 24px; /* Increased padding */
            text-align: left;
            font-size: 12px;
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
            padding: 16px 24px; /* Increased padding */
            font-size: 14px; /* Increased from 11px */
            color: #111827;
            border-bottom: 1px solid #f3f4f6;
        }

        .items-table td.text-right {
            text-align: right;
        }

        .items-table td.font-medium {
            font-weight: 500;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Totals */
        .totals-section {
            width: 100%;
            margin-bottom: 32px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-spacer {
            width: 60%;
        }

        .totals-content {
            width: 40%;
            vertical-align: top;
        }

        .totals-box {
            background-color: #f9fafb;
            padding: 24px; /* Increased from 16px */
            border-radius: 8px;
            width: 256px; /* Increased from 200px */
        }

        .total-row {
            width: 100%;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
        }

        .total-row:last-child {
            margin-bottom: 0;
        }

        .total-label {
            font-size: 14px; /* Increased from 12px */
            color: #6b7280;
        }

        .total-value {
            font-size: 14px; /* Increased from 12px */
            color: #111827;
            font-weight: 500;
            text-align: right;
        }

        .total-row.grand-total {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            margin-top: 12px;
            font-size: 18px; /* Increased from 14px */
            font-weight: 700;
        }

        .total-row.balance-due .total-label,
        .total-row.balance-due .total-value {
            color: #dc2626;
            font-weight: 600;
            font-size: 14px;
        }

        /* Notes */
        .notes-section {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid #e5e7eb;
        }

        .notes-title {
            font-size: 14px; /* Increased from 12px */
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }

        .notes-content {
            font-size: 14px; /* Increased from 12px */
            color: #6b7280;
            white-space: pre-wrap;
            line-height: 1.5;
        }

        /* Footer */
        .footer {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .footer-content {
            font-size: 14px; /* Increased from 11px */
            color: #6b7280;
            line-height: 1.4;
        }

        .footer-content div {
            margin-bottom: 4px;
        }

        .footer-content .thank-you {
            font-size: 12px;
            font-weight: 400;
            color: #6b7280;
            margin-top: 8px;
        }

        .footer-content a {
            color: #2563eb;
            text-decoration: none;
        }

        .footer-content a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        /* Ensure proper text wrapping */
        .items-table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .address-content div {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Prevent table overflow */
        .items-table {
            table-layout: fixed;
        }

        .items-table th:nth-child(1),
        .items-table td:nth-child(1) {
            width: 40%;
        }

        .items-table th:nth-child(2),
        .items-table td:nth-child(2) {
            width: 15%;
        }

        .items-table th:nth-child(3),
        .items-table td:nth-child(3) {
            width: 20%;
        }

        .items-table th:nth-child(4),
        .items-table td:nth-child(4) {
            width: 25%;
        }
    </style>
</head>
<body>
    <div class="page">
            <!-- Header -->
            <div class="header">
                <table class="header-table">
                    <tr>
                        <td class="header-left">
                            <h1>INVOICE</h1>
                        </td>
                        <td class="header-right">
                            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                            <div class="invoice-dates">
                                <div>Issue Date: {{ $invoice->issue_date->format('M j, Y') }}</div>
                                <div>Due Date: {{ $invoice->due_date->format('M j, Y') }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- From/To Section -->
            <div class="address-section">
                <table class="address-table">
                    <tr>
                        <td>
                            <div class="section-title">From</div>
                            <div class="address-content">
                                <div class="address-details">
                                    <div>{{ $companySettings->person_name }}</div>
                                    <div>{{ $companySettings->address }}</div>
                                    <div>
                                        {{ $companySettings->city }}@if($companySettings->city && $companySettings->state), @endif{{ $companySettings->state }} {{ $companySettings->zip_code }}
                                    </div>
                                    @if($companySettings->country)
                                        <div>{{ $companySettings->country }}</div>
                                    @endif
                                </div>
                                @if($companySettings->phone || $companySettings->email)
                                    <div class="contact-info">
                                        @if($companySettings->phone)
                                            <div>{{ $companySettings->phone }}</div>
                                        @endif
                                        @if($companySettings->email)
                                            <div>{{ $companySettings->email }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="section-title">Bill To</div>
                            <div class="address-content">
                                <div class="address-details">
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
                                    <div class="contact-info">
                                        <div>{{ $invoice->customer->phone }}</div>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Items Table -->
            <div class="items-container">
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
                                <td class="text-right font-medium">{{ \App\Helpers\CurrencyHelper::format($item->total_amount, $invoice->currency) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td class="totals-spacer"></td>
                        <td class="totals-content">
                            <div class="totals-box">
                                <div class="total-row">
                                    <span class="total-label">Subtotal:</span>
                                    <span class="total-value">{{ $invoice->getFormattedSubtotal() }}</span>
                                </div>
                                @if($invoice->tax_amount > 0)
                                    <div class="total-row">
                                        <span class="total-label">Tax:</span>
                                        <span class="total-value">{{ $invoice->getFormattedTaxAmount() }}</span>
                                    </div>
                                @endif
                                <div class="total-row grand-total">
                                    <span class="total-label">Total:</span>
                                    <span class="total-value">{{ $invoice->getFormattedTotalAmount() }}</span>
                                </div>
                                @if($invoice->status !== 'paid')
                                    <div class="total-row balance-due">
                                        <span class="total-label">Balance Due:</span>
                                        <span class="total-value">{{ $invoice->getFormattedTotalAmount() }}</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>
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
</body>
</html> 
<x-mail::message>
<p>Hi {{ $customer->person_name ?: $customer->name }},</p>

<p>A new invoice has been generated for you by {{ $companySettings->person_name }}{{ $companySettings->company_name ? ' (' . $companySettings->company_name . ')' : '' }}. Here's a quick summary:</p>

<p><strong>Invoice Details:</strong> {{ $invoice->invoice_number }} -</p>

<p><strong>Total Invoice Amount:</strong> {{ $invoice->getFormattedTotalAmount() }}</p>

<p><strong>Due Date:</strong> {{ $invoice->due_date->format('M d Y') }}</p>

<p>You can view the invoice or download a PDF copy of it from the following link:</p>

<x-mail::button :url="$publicUrl">
    View Invoice
</x-mail::button>

<p>Best regards,<br>{{ $companySettings->person_name }}</p>
</x-mail::message>
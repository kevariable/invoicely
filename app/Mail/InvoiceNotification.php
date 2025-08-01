<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\CompanySetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public CompanySetting $companySettings
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Invoice: ' . $this->invoice->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-notification',
            with: [
                'invoice' => $this->invoice,
                'companySettings' => $this->companySettings,
                'customer' => $this->invoice->customer,
                'publicUrl' => $this->invoice->getPublicUrl(),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
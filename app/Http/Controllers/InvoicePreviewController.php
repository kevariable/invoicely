<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class InvoicePreviewController extends Controller
{
    /**
     * Display the invoice for public preview using the secure token.
     */
    public function show(string $token): View|Response
    {
        $invoice = Invoice::where('public_token', $token)
            ->with(['customer', 'items'])
            ->first();

        if (!$invoice) {
            abort(404, 'Invoice not found or link has expired.');
        }

        // Mark invoice as viewed if it's currently unread
        $invoice->markAsViewed();

        $companySettings = CompanySetting::getSettings();

        return view('invoice-public-preview', [
            'invoice' => $invoice,
            'companySettings' => $companySettings,
        ]);
    }

    /**
     * Download PDF version of the invoice using the secure token.
     */
    public function downloadPdf(string $token)
    {
        $invoice = Invoice::where('public_token', $token)
            ->with(['customer', 'items'])
            ->first();

        if (!$invoice) {
            abort(404, 'Invoice not found or link has expired.');
        }

        // Mark invoice as viewed if it's currently unread
        $invoice->markAsViewed();

        $companySettings = CompanySetting::getSettings();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice-pdf', [
            'invoice' => $invoice,
            'companySettings' => $companySettings
        ]);

        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';

        return $pdf->download($filename);
    }
}
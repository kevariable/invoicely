<?php

namespace Invoice\Invoice\Domain\Actions;

use App\Models\CompanySetting;
use App\Models\Invoice;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

final readonly class GenerateInvoiceAction
{
    public function execute(Invoice $invoice): string
    {
        $companySettings = CompanySetting::getSettings();

        $invoice->load(['customer', 'items']);

        $html = View::make('invoice-pdf-browsershot', [
            'invoice' => $invoice,
            'companySettings' => $companySettings,
        ])->render();

        return Browsershot::html($html)
            ->noSandbox()
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->pdf();
    }
}

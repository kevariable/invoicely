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
        
        $html = View::make('invoice-pdf-tailwind', [
            'invoice' => $invoice,
            'companySettings' => $companySettings,
        ])->render();

        return Browsershot::html($html)
            ->fullPage()
            ->setNodeBinary('/usr/bin/node')
            ->setNpmBinary('/usr/bin/npm')
            ->setChromePath('/usr/bin/chromium-headless-shell')
            ->noSandbox()
            ->format('A4')
            ->margins(15, 15, 15, 15)
            ->showBackground()
            ->pdf();
    }
} 
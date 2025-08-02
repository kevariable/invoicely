<?php

namespace Invoice\Invoice\Domain\Actions;

use App\Models\CompanySetting;
use App\Models\Invoice;
use Illuminate\Support\Facades\View;
 use Spatie\Browsershot\Browsershot;
//use Barryvdh\DomPDF\Facade\Pdf;

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

        // Commented out Spatie Browsershot implementation
         return Browsershot::html($html)
             ->fullPage()
//             ->setNodeBinary('/usr/bin/node')
//             ->setNpmBinary('/usr/bin/npm')
             ->setChromePath('/var/www/html/chrome-headless-shell/linux_arm-138.0.7204.183/chrome-headless-shell-linux64/chrome-headless-shell')
             ->noSandbox()
             ->format('A4')
             ->margins(15, 15, 15, 15)
             ->showBackground()
             ->pdf();

        // New DomPDF implementation
//        return Pdf::loadHTML($html)
//            ->setPaper('A4', 'portrait')
//            ->setOptions([
//                'isHtml5ParserEnabled' => true,
//                'isRemoteEnabled' => true,
//                'defaultFont' => 'Arial',
//                'chroot' => public_path(),
//            ])
//            ->output();
    }
}

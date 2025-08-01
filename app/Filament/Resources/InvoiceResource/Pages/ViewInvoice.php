<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\CompanySetting;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Open Preview')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => $this->record->getPublicUrl())
                ->openUrlInNewTab(),
            
            Actions\Action::make('copy_share_link')
                ->label('Copy Share Link')
                ->icon('heroicon-o-share')
                ->color('info')
                ->action(function () {
                    $url = $this->record->getPublicUrl();
                    session()->flash('share_url', $url);
                })
                ->after(function () {
                    Notification::make()
                        ->title('Share link generated!')
                        ->body('Share URL: ' . $this->record->getPublicUrl())
                        ->success()
                        ->persistent()
                        ->send();
                }),
            
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->action(function () {
                    $companySettings = CompanySetting::getSettings();
                    
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice-pdf', [
                        'invoice' => $this->record->load(['customer', 'items']),
                        'companySettings' => $companySettings
                    ]);
                    
                    $filename = 'invoice-' . $this->record->invoice_number . '.pdf';
                    
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $filename, [
                        'Content-Type' => 'application/pdf',
                    ]);
                }),
                
            Actions\Action::make('mark_paid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Mark Invoice as Paid')
                ->modalDescription('Are you sure you want to mark this invoice as paid?')
                ->action(function () {
                    $this->record->markAsPaid();
                    $this->refreshFormData(['status', 'paid_date']);
                })
                ->visible(fn () => !$this->record->isPaid())
                ->after(fn () => Notification::make()
                    ->title('Invoice marked as paid')
                    ->success()
                    ->send()),
                    
            Actions\EditAction::make(),
        ];
    }
}

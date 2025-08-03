<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Mail\InvoiceNotification;
use App\Models\CompanySetting;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;
use Invoice\Invoice\Domain\Actions\GenerateInvoiceAction;

/**
 * @property \App\Models\Invoice $record
 */
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

            InvoiceResource\Actions\CopyShareLinkAction::make('copy_share_link')
                ->copyable(fn () => $this->record->getPublicUrl())
                ->label('Copy Share Link')
                ->icon('heroicon-o-share')
                ->color('info'),

            Actions\Action::make('send_email')
                ->label('Send Email')
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Send Invoice Email')
                ->modalDescription(fn () => 'Send invoice '.$this->record->invoice_number.' to '.$this->record->customer->name.' ('.$this->record->customer->email.')?')
                ->action(function () {
                    $companySettings = CompanySetting::getSettings();

                    Mail::to($this->record->customer->email)->send(
                        new InvoiceNotification($this->record->load(['customer', 'items']), $companySettings)
                    );
                })
                ->after(function () {
                    Notification::make()
                        ->title('Email sent successfully!')
                        ->body('Invoice has been sent to '.$this->record->customer->email)
                        ->success()
                        ->send();
                })
                ->visible(fn () => ! empty($this->record->customer->email)),

            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn () => route('invoice.public.download', $this->record->public_token)),

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
                ->visible(fn () => ! $this->record->isPaid())
                ->after(fn () => Notification::make()
                    ->title('Invoice marked as paid')
                    ->success()
                    ->send()),

            Actions\EditAction::make(),
        ];
    }
}

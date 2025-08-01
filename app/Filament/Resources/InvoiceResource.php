<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Actions\Tables\CopyShareLinkAction;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Helpers\CurrencyHelper;
use App\Mail\InvoiceNotification;
use App\Models\CompanySetting;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Invoice\Invoice\Domain\Actions\GenerateInvoiceAction;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Details')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => Invoice::generateInvoiceNumber()),

                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'paid' => 'Paid',
                                'overdue' => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('draft'),

                        Forms\Components\DatePicker::make('issue_date')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('due_date')
                            ->required()
                            ->default(now()->addDays(30)),
                    ])->columns(2),

                Forms\Components\Section::make('Financial Details')
                    ->schema([
                        Forms\Components\Select::make('currency')
                            ->label('Currency')
                            ->required()
                            ->options(CurrencyHelper::getSelectOptions())
                            ->default(CurrencyHelper::getDefaultCurrency())
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefixIcon('heroicon-o-currency-dollar')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('tax_amount')
                            ->numeric()
                            ->prefixIcon('heroicon-o-currency-dollar')
                            ->default(0),

                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefixIcon('heroicon-o-currency-dollar')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(4),

                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('issue_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => str($state)->title())
                    ->colors([
                        'secondary' => 'draft',
                        'primary' => 'sent',
                        'success' => 'paid',
                        'danger' => 'overdue',
                        'warning' => 'cancelled',
                    ]),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(function ($record) {
                        return $record->getFormattedTotalAmount();
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'USD' => 'primary',
                        'GBP' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('view_state')
                    ->badge()
                    ->label('View Status')
                    ->colors([
                        'warning' => 'unread',
                        'success' => 'viewed',
                    ])
                    ->icons([
                        'heroicon-o-eye-slash' => 'unread',
                        'heroicon-o-eye' => 'viewed',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('view_state')
                    ->label('View Status')
                    ->options([
                        'unread' => 'Unread',
                        'viewed' => 'Viewed',
                    ]),

                Tables\Filters\SelectFilter::make('currency')
                    ->options([
                        'USD' => '$ USD',
                        'GBP' => '£ GBP',
                    ]),

                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->where('due_date', '<', now())->where('status', '!=', 'paid'))
                    ->label('Overdue Invoices'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Invoice'),

                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Invoice $record) => $record->getPublicUrl())
                    ->openUrlInNewTab(),

                CopyShareLinkAction::make('copy_share_link')
                    ->copyable(fn (Invoice $record) => $record->getPublicUrl())
                    ->label('Copy Share Link')
                    ->icon('heroicon-o-share')
                    ->color('info'),

                Tables\Actions\Action::make('send_email')
                    ->label('Send Email')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Send Invoice Email')
                    ->modalDescription(fn (Invoice $record) => 'Send invoice '.$record->invoice_number.' to '.$record->customer->name.' ('.$record->customer->email.')?')
                    ->action(function (Invoice $record) {
                        $companySettings = CompanySetting::getSettings();

                        Mail::to($record->customer->email)->send(
                            new InvoiceNotification($record->load(['customer', 'items']), $companySettings)
                        );
                    })
                    ->after(function (Invoice $record) {
                        \Filament\Notifications\Notification::make()
                            ->title('Email sent successfully!')
                            ->body('Invoice has been sent to '.$record->customer->email)
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Invoice $record) => ! empty($record->customer->email)),

                Tables\Actions\Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (Invoice $record) {
                        $pdfService = new GenerateInvoiceAction;
                        $pdfContent = $pdfService->execute($record);

                        $filename = 'invoice-'.$record->invoice_number.'.pdf';

                        return response($pdfContent)
                            ->header('Content-Type', 'application/pdf')
                            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
                    }),

                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Invoice as Paid')
                    ->modalDescription('Are you sure you want to mark this invoice as paid?')
                    ->action(fn (Invoice $record) => $record->markAsPaid())
                    ->visible(fn (Invoice $record) => ! $record->isPaid())
                    ->after(fn () => \Filament\Notifications\Notification::make()
                        ->title('Invoice marked as paid')
                        ->success()
                        ->send()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Helpers\CurrencyHelper;
use App\Models\Bill;
use App\Models\CompanySetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Bill';

    protected static ?string $pluralModelLabel = 'Bills';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bill details')
                    ->schema([
                        Forms\Components\TextInput::make('vendor_name')
                            ->label('Vendor')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category')
                            ->options([
                                'hosting' => 'Hosting',
                                'software' => 'Software',
                                'domain' => 'Domain',
                                'service' => 'Service',
                                'subscription' => 'Subscription',
                                'other' => 'Other',
                            ])
                            ->searchable()
                            ->nullable(),

                        Forms\Components\TextInput::make('description')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Amount')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefixIcon('heroicon-o-currency-dollar'),

                        Forms\Components\Select::make('currency')
                            ->required()
                            ->options(CurrencyHelper::getSelectOptions())
                            ->default(fn () => CompanySetting::getSettings()->currency ?? 'USD'),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'paid' => 'Paid',
                            ])
                            ->required()
                            ->default('unpaid')
                            ->live(),

                        Forms\Components\Toggle::make('recurring')
                            ->label('Recurring (e.g. monthly)'),

                        Forms\Components\DatePicker::make('due_date'),

                        Forms\Components\DateTimePicker::make('paid_date')
                            ->visible(fn (callable $get) => $get('status') === 'paid'),
                    ])->columns(2),

                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('due_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('vendor_name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn ($record) => $record->getFormattedAmount())
                    ->sortable(),

                Tables\Columns\IconColumn::make('recurring')
                    ->label('Recurring')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => str($state)->title())
                    ->colors([
                        'warning' => 'unpaid',
                        'success' => 'paid',
                    ]),

                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_date')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'unpaid' => 'Unpaid',
                    'paid' => 'Paid',
                ]),
                Tables\Filters\SelectFilter::make('category')->options([
                    'hosting' => 'Hosting',
                    'software' => 'Software',
                    'domain' => 'Domain',
                    'service' => 'Service',
                    'subscription' => 'Subscription',
                    'other' => 'Other',
                ]),
                Tables\Filters\TernaryFilter::make('recurring'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Bill $record) => ! $record->isPaid())
                    ->action(fn (Bill $record) => $record->markAsPaid())
                    ->after(fn () => Notification::make()
                        ->title('Bill marked as paid')
                        ->success()
                        ->send()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'edit' => Pages\EditBill::route('/{record}/edit'),
        ];
    }
}

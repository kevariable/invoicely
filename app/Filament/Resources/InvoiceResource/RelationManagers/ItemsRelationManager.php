<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->placeholder('Describe the service or product...')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('quantity')
                    ->label('Quantity')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->placeholder('e.g., 8.5 (hours), 10 (pieces), etc.')
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $rate = (float) ($get('unit_rate') ?? 0);
                        $qty = (float) ($state ?? 0);
                        $set('total_amount', round($qty * $rate, 2));
                    }),

                Forms\Components\TextInput::make('unit_rate')
                    ->label('Unit Rate')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->prefixIcon('heroicon-o-currency-dollar')
                    ->placeholder('Rate per unit/hour/piece')
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $quantity = (float) ($get('quantity') ?? 0);
                        $rate = (float) ($state ?? 0);
                        $set('total_amount', round($quantity * $rate, 2));
                    }),

                Forms\Components\TextInput::make('total_amount')
                    ->label('Total Amount')
                    ->required()
                    ->numeric()
                    ->prefixIcon('heroicon-o-currency-dollar')
                    ->disabled()
                    ->dehydrated()
                    ->default(0),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_rate')
                    ->label('Unit Rate')
                    ->formatStateUsing(function ($record) {
                        $currency = $record->invoice->currency ?? 'USD';

                        return \App\Helpers\CurrencyHelper::format($record->unit_rate, $currency);
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(function ($record) {
                        $currency = $record->invoice->currency ?? 'USD';

                        return \App\Helpers\CurrencyHelper::format($record->total_amount, $currency);
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Line Item'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort')
            ->defaultSort('sort');
    }
}

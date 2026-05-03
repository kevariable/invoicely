<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentInvoicesTable extends BaseWidget
{
    protected static ?string $heading = 'Recent invoices';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->with('customer')
                    ->latest('issue_date')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->label('Invoice'),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn ($record) => $record->getFormattedTotalAmount()),
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
                Tables\Columns\TextColumn::make('view_state')
                    ->label('Viewed')
                    ->badge()
                    ->colors([
                        'warning' => 'unread',
                        'success' => 'viewed',
                    ]),
                Tables\Columns\TextColumn::make('issue_date')->date(),
            ])
            ->paginated(false);
    }
}

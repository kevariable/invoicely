<?php

namespace App\Filament\Widgets;

use App\Helpers\CurrencyHelper;
use App\Models\Bill;
use App\Models\CompanySetting;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BalanceOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $currency = CompanySetting::getSettings()->currency ?? 'USD';

        $income = (float) Invoice::where('status', 'paid')->sum('total_amount');
        $expenses = (float) Bill::where('status', 'paid')->sum('amount');
        $net = $income - $expenses;

        $netColor = $net >= 0 ? 'success' : 'danger';
        $netIcon = $net >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down';

        return [
            Stat::make('Income', CurrencyHelper::format($income, $currency))
                ->description('Paid invoices')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Expenses', CurrencyHelper::format($expenses, $currency))
                ->description('Paid bills')
                ->color('warning')
                ->icon('heroicon-o-credit-card'),

            Stat::make('Net balance', CurrencyHelper::format($net, $currency))
                ->description('Income − Expenses')
                ->color($netColor)
                ->icon($netIcon),
        ];
    }
}

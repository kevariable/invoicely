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
        $invoiceCurrencies = Invoice::where('status', 'paid')->distinct()->pluck('currency');
        $billCurrencies = Bill::where('status', 'paid')->distinct()->pluck('currency');

        $currencies = $invoiceCurrencies->merge($billCurrencies)->filter()->unique()->values();
        if ($currencies->isEmpty()) {
            $currencies = collect([CompanySetting::getSettings()->currency ?? 'USD']);
        }

        $stats = [];
        foreach ($currencies as $currency) {
            $income = (float) Invoice::where('status', 'paid')->where('currency', $currency)->sum('total_amount');
            $expenses = (float) Bill::where('status', 'paid')->where('currency', $currency)->sum('amount');
            $net = $income - $expenses;

            $netColor = $net >= 0 ? 'success' : 'danger';
            $netIcon = $net >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down';

            $stats[] = Stat::make("Income ({$currency})", CurrencyHelper::format($income, $currency))
                ->description('Paid invoices')
                ->color('success')
                ->icon('heroicon-o-banknotes');

            $stats[] = Stat::make("Expenses ({$currency})", CurrencyHelper::format($expenses, $currency))
                ->description('Paid bills')
                ->color('warning')
                ->icon('heroicon-o-credit-card');

            $stats[] = Stat::make("Net balance ({$currency})", CurrencyHelper::format($net, $currency))
                ->description('Income − Expenses')
                ->color($netColor)
                ->icon($netIcon);
        }

        return $stats;
    }
}

<?php

namespace App\Filament\Widgets;

use App\Helpers\CurrencyHelper;
use App\Models\CompanySetting;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class InvoiceStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $currency = CompanySetting::getSettings()->currency ?? 'USD';

        $totalEarned = (float) Invoice::where('status', 'paid')->sum('total_amount');
        $outstanding = (float) Invoice::whereIn('status', ['sent', 'overdue'])->sum('total_amount');

        $overdueQuery = Invoice::where('due_date', '<', now())->where('status', '!=', 'paid');
        $overdueAmount = (float) (clone $overdueQuery)->sum('total_amount');
        $overdueCount = (clone $overdueQuery)->count();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $thisMonth = (float) Invoice::where('status', 'paid')
            ->whereBetween('paid_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        return [
            Stat::make('Total earned', CurrencyHelper::format($totalEarned, $currency))
                ->description('Paid invoices')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Outstanding', CurrencyHelper::format($outstanding, $currency))
                ->description('Sent + overdue')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Overdue', CurrencyHelper::format($overdueAmount, $currency))
                ->description($overdueCount.' '.($overdueCount === 1 ? 'invoice' : 'invoices'))
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make('Paid this month', CurrencyHelper::format($thisMonth, $currency))
                ->description($startOfMonth->format('F Y'))
                ->color('primary')
                ->icon('heroicon-o-calendar'),
        ];
    }
}

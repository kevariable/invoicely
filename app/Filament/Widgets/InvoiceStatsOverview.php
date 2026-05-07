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
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $currencies = Invoice::query()
            ->where(function ($q) {
                $q->where('status', 'paid')
                    ->orWhereIn('status', ['sent', 'overdue'])
                    ->orWhere(function ($qq) {
                        $qq->where('due_date', '<', now())->where('status', '!=', 'paid');
                    });
            })
            ->distinct()
            ->pluck('currency')
            ->filter()
            ->values();

        if ($currencies->isEmpty()) {
            $currencies = collect([CompanySetting::getSettings()->currency ?? 'USD']);
        }

        $stats = [];
        foreach ($currencies as $currency) {
            $totalEarned = (float) Invoice::where('status', 'paid')
                ->where('currency', $currency)
                ->sum('total_amount');

            $outstanding = (float) Invoice::whereIn('status', ['sent', 'overdue'])
                ->where('currency', $currency)
                ->sum('total_amount');

            $overdueQuery = Invoice::where('due_date', '<', now())
                ->where('status', '!=', 'paid')
                ->where('currency', $currency);
            $overdueAmount = (float) (clone $overdueQuery)->sum('total_amount');
            $overdueCount = (clone $overdueQuery)->count();

            $thisMonth = (float) Invoice::where('status', 'paid')
                ->where('currency', $currency)
                ->whereBetween('paid_date', [$startOfMonth, $endOfMonth])
                ->sum('total_amount');

            $stats[] = Stat::make("Total earned ({$currency})", CurrencyHelper::format($totalEarned, $currency))
                ->description('Paid invoices')
                ->color('success')
                ->icon('heroicon-o-banknotes');

            $stats[] = Stat::make("Outstanding ({$currency})", CurrencyHelper::format($outstanding, $currency))
                ->description('Sent + overdue')
                ->color('warning')
                ->icon('heroicon-o-clock');

            $stats[] = Stat::make("Overdue ({$currency})", CurrencyHelper::format($overdueAmount, $currency))
                ->description($overdueCount.' '.($overdueCount === 1 ? 'invoice' : 'invoices'))
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle');

            $stats[] = Stat::make("Paid this month ({$currency})", CurrencyHelper::format($thisMonth, $currency))
                ->description($startOfMonth->format('F Y'))
                ->color('primary')
                ->icon('heroicon-o-calendar');
        }

        return $stats;
    }
}

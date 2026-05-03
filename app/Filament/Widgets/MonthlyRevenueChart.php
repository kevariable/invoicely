<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly revenue (paid invoices)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $start = Carbon::now()->subMonths(11)->startOfMonth();

        $driver = DB::connection()->getDriverName();
        $bucketExpr = match ($driver) {
            'pgsql' => "to_char(paid_date, 'YYYY-MM')",
            'mysql', 'mariadb' => "DATE_FORMAT(paid_date, '%Y-%m')",
            default => "strftime('%Y-%m', paid_date)",
        };

        $rows = Invoice::query()
            ->where('status', 'paid')
            ->whereNotNull('paid_date')
            ->where('paid_date', '>=', $start)
            ->selectRaw("$bucketExpr as bucket, sum(total_amount) as total")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->pluck('total', 'bucket');

        $labels = [];
        $data = [];
        $cursor = $start->copy();

        for ($i = 0; $i < 12; $i++) {
            $key = $cursor->format('Y-m');
            $labels[] = $cursor->format('M Y');
            $data[] = (float) ($rows[$key] ?? 0);
            $cursor->addMonth();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

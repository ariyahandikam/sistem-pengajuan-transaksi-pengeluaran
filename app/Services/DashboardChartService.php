<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Submission;
use Carbon\Carbon;

class DashboardChartService
{
    /**
     * Menghitung data submission berdasarkan periode
     */
    public function getSubmissionChartData(string $period = 'monthly'): array
    {
        return match ($period) {
            'daily'    => $this->getDailyData(),
            'weekly'   => $this->getWeeklyData(),
            'monthly'  => $this->getMonthlyData(),
            'yearly'   => $this->getYearlyData(),
            default    => $this->getMonthlyData(),
        };
    }

    /**
     * Data submission per hari (30 hari terakhir)
     */
    private function getDailyData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M');
            
            $count = Submission::whereDate('created_at', $date)
                ->count();
            $data[] = $count;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'period' => 'Harian (30 hari terakhir)',
        ];
    }

    /**
     * Data submission per minggu (12 minggu terakhir)
     */
    private function getWeeklyData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
            
            $labels[] = $startDate->format('d M') . ' - ' . $endDate->format('d M');
            
            $count = Submission::whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $data[] = $count;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'period' => 'Mingguan (12 minggu terakhir)',
        ];
    }

    /**
     * Data submission per bulan (12 bulan terakhir)
     */
    private function getMonthlyData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $count = Submission::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'period' => 'Bulanan (12 bulan terakhir)',
        ];
    }

    /**
     * Data submission per tahun (5 tahun terakhir)
     */
    private function getYearlyData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $labels[] = (string) $year;
            
            $count = Submission::whereYear('created_at', $year)
                ->count();
            $data[] = $count;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'period' => 'Tahunan (5 tahun terakhir)',
        ];
    }

    /**
     * Menghitung pengeluaran per kategori dalam persentase
     */
    public function getCategoryExpensePercentage(): array
    {
        $categories = Category::query()->pluck('name')->all();
        $categoryTotals = [];

        foreach ($categories as $categoryName) {
            $categoryTotals[$categoryName] = 0;
        }

        $submissions = Submission::with('category')
            ->whereNotIn('status', [Submission::STATUS_DRAFT])
            ->get();

        $grandTotal = $submissions->sum('amount');

        foreach ($submissions as $submission) {
            $categoryName = $submission->category?->name ?? 'Lainnya';

            if (!array_key_exists($categoryName, $categoryTotals)) {
                $categoryTotals[$categoryName] = 0;
            }

            $categoryTotals[$categoryName] += (float) $submission->amount;
        }

        $labels = array_keys($categoryTotals);
        $amounts = array_values($categoryTotals);
        $percentages = [];

        foreach ($amounts as $amount) {
            $percentages[] = $grandTotal > 0 ? round(($amount / $grandTotal) * 100, 2) : 0;
        }

        return [
            'labels' => $labels,
            'data' => $percentages,
            'amounts' => $amounts,
        ];
    }

    /**
     * Mendapatkan laporan pengeluaran detail per kategori
     */
    public function getExpenseReport(array $filters = []): array
    {
        $report = [];
        $categories = Category::query()->pluck('name')->all();

        foreach ($categories as $categoryName) {
            $report[$categoryName] = [
                'count' => 0,
                'total' => 0,
            ];
        }

        $query = Submission::with('category')
            ->whereNotIn('status', [Submission::STATUS_DRAFT]);

        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }
        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        $submissions = $query->get();

        $grandTotal = 0;

        foreach ($submissions as $submission) {
            $categoryName = $submission->category?->name ?? 'Lainnya';

            if (!isset($report[$categoryName])) {
                $report[$categoryName] = [
                    'count' => 0,
                    'total' => 0,
                ];
            }

            $report[$categoryName]['count'] += 1;
            $report[$categoryName]['total'] += (float) $submission->amount;
            $grandTotal += (float) $submission->amount;
        }

        foreach ($report as &$item) {
            $item['percentage'] = ($grandTotal > 0) ? round(($item['total'] / $grandTotal) * 100, 2) : 0;
        }

        return [
            'report' => $report,
            'grandTotal' => $grandTotal,
        ];
    }
}

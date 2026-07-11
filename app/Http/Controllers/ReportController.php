<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Services\DashboardChartService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private DashboardChartService $chartService)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['from', 'to', 'category']);

        // For now use the service report (global). Filtering can be added later.
        $expenseReport = $this->chartService->getExpenseReport($filters);

        return view('reports.index', [
            'expenseReport' => $expenseReport,
            'filters' => $filters,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        // Build same report but with query filters if provided
        $query = Submission::with('category')
            ->whereNotIn('status', [Submission::STATUS_DRAFT]);

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        $rows = $query->get()->groupBy(fn($i) => $i->category?->name ?? 'Lainnya');

        $response = new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kategori', 'Jumlah Pengajuan', 'Total Pengeluaran']);

            foreach ($rows as $category => $items) {
                $count = $items->count();
                $total = $items->sum('amount');
                fputcsv($handle, [$category, $count, $total]);
            }

            // grand total
            $grand = $rows->flatten()->sum('amount');
            fputcsv($handle, ['Total', '', $grand]);

            fclose($handle);
        });

        $filename = 'expense-report-'.date('Ymd_His').'.csv';

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        return $response;
    }

    public function exportPdf(Request $request)
    {
        // Build full report (apply filters if provided)
        $filters = $request->only(['from', 'to', 'category']);
        $expenseReport = $this->chartService->getExpenseReport($filters);

        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(500, 'PDF export requires barryvdh/laravel-dompdf. Run: composer require barryvdh/laravel-dompdf');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', ['expenseReport' => $expenseReport]);

        return $pdf->download('expense-report-'.date('Ymd_His').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            abort(500, 'Excel export requires maatwebsite/excel. Run: composer require maatwebsite/excel');
        }

        $fileName = 'expense-report-'.date('Ymd_His').'.xlsx';

        $filters = $request->only(['from', 'to', 'category']);

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExpenseReportExport($filters), $fileName);
    }
}

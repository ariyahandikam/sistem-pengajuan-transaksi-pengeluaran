<?php

namespace App\Exports;

use App\Models\Submission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class ExpenseReportExport implements FromCollection, WithHeadings
{
    protected array $filters = [];

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }
    public function collection()
    {
        $query = Submission::with('category')
            ->whereNotIn('status', [Submission::STATUS_DRAFT]);

        if (!empty($this->filters['from'])) {
            $query->whereDate('created_at', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['to'])) {
            $query->whereDate('created_at', '<=', $this->filters['to']);
        }
        if (!empty($this->filters['category'])) {
            $query->where('category_id', $this->filters['category']);
        }

        $rows = $query->get()->groupBy(fn($i) => $i->category?->name ?? 'Lainnya');

        $collection = [];

        foreach ($rows as $category => $items) {
            $collection[] = [
                'category' => $category,
                'count' => $items->count(),
                'total' => $items->sum('amount'),
            ];
        }

        // append grand total
        $grand = $rows->flatten()->sum('amount');
        $collection[] = ['category' => 'Total', 'count' => '', 'total' => $grand];

        return new Collection($collection);
    }

    public function headings(): array
    {
        return ['Kategori', 'Jumlah Pengajuan', 'Total Pengeluaran'];
    }
}

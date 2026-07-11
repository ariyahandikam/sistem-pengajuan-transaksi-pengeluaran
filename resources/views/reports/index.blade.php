<x-app-layout>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>Laporan Pengeluaran
            </h4>
            <p class="text-muted small mb-0">Ringkasan pengeluaran berdasarkan kategori</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.export', request()->all()) }}" class="btn btn-light border text-success shadow-sm rounded-pill" data-bs-toggle="tooltip" title="Export CSV">
                <i class="bi bi-file-earmark-spreadsheet"></i> CSV
            </a>
            <a href="{{ route('reports.export.excel', request()->all()) }}" class="btn btn-light border text-success shadow-sm rounded-pill" data-bs-toggle="tooltip" title="Export Excel">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="{{ route('reports.export.pdf', request()->all()) }}" class="btn btn-light border text-danger shadow-sm rounded-pill" data-bs-toggle="tooltip" title="Export PDF">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4 fade-in-up">
        <div class="card-body">
            <form method="get" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Dari Tanggal</label>
                    <input type="date" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}" />
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}" />
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach(App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}" {{ (isset($filters['category']) && $filters['category'] == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1" type="submit">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm border-0 fade-in-up">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Kategori</th>
                            <th class="text-center">Jumlah Pengajuan</th>
                            <th class="text-end">Total Pengeluaran</th>
                            <th class="text-end pe-4" style="width: 25%;">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenseReport['report'] as $category => $data)
                            <tr>
                                <td class="ps-4 fw-medium text-dark">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3">
                                            <i class="bi bi-tag-fill"></i>
                                        </div>
                                        {{ $category }}
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-light text-dark border">{{ $data['count'] }} Pengajuan</span>
                                </td>
                                <td class="text-end align-middle fw-medium">
                                    Rp {{ number_format($data['total'], 0, ',', '.') }}
                                </td>
                                <td class="pe-4 align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="progress flex-grow-1 me-3" style="height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: {{ min($data['percentage'], 100) }}%"></div>
                                        </div>
                                        <span class="small fw-bold text-muted" style="min-width: 40px; text-align: right;">{{ $data['percentage'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-file-earmark-x fs-1 d-block mb-3"></i>
                                        <p class="mb-0 fs-5">Belum ada data laporan</p>
                                        <small>Ubah filter tanggal atau kategori untuk melihat data.</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        @if(count($expenseReport['report']) > 0)
                            <tr class="bg-light">
                                <td class="ps-4 fw-bold text-dark text-uppercase" colspan="2">Total Keseluruhan</td>
                                <td class="text-end fw-bold text-primary fs-5 align-middle">
                                    Rp {{ number_format($expenseReport['grandTotal'], 0, ',', '.') }}
                                </td>
                                <td class="pe-4 align-middle text-end fw-bold text-primary">
                                    100%
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

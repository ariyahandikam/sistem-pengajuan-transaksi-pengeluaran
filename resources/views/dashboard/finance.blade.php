<x-app-layout>
    <x-slot name="header">Dashboard Finance</x-slot>
    

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-primary">
                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div class="me-3">
                                    <div class="summary-title text-muted">Total Pengajuan</div>
                                    <div class="summary-value text-dark" data-target="{{ $totalPengajuan }}">0</div>
                                </div>
                    <div class="summary-icon">
                        <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-warning">
                <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="me-3">
                        <div class="summary-title text-muted">Menunggu Approval</div>
                        <div class="summary-value text-dark" data-target="{{ $menungguApproval }}">0</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-danger">
                <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="me-3">
                        <div class="summary-title text-muted">Rejected</div>
                        <div class="summary-value text-dark" data-target="{{ $ditolak }}">0</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-success">
                <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="me-3">
                        <div class="summary-title text-muted">Paid</div>
                        <div class="summary-value text-dark" data-target="{{ $paid }}">0</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-check2-circle text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section (moved above Expense Report) -->
    <div class="row g-4 mb-4">
        <!-- Submission Trend Chart -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i> Trend Pengajuan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="?period=daily" class="btn btn-outline-primary {{ $activePeriod === 'daily' ? 'active' : '' }}">Harian</a>
                            <a href="?period=weekly" class="btn btn-outline-primary {{ $activePeriod === 'weekly' ? 'active' : '' }}">Mingguan</a>
                            <a href="?period=monthly" class="btn btn-outline-primary {{ $activePeriod === 'monthly' ? 'active' : '' }}">Bulanan</a>
                            <a href="?period=yearly" class="btn btn-outline-primary {{ $activePeriod === 'yearly' ? 'active' : '' }}">Tahunan</a>
                        </div>
                    </div>
                    <div class="chart-container mb-2"><canvas id="submissionChart"></canvas></div>
                </div>
            </div>
        </div>

        <!-- Category Expense Chart -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i> Pengeluaran per Kategori
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container mb-2"><canvas id="categoryChart"></canvas></div>
                    @php
                        $chartCategoryColors = \App\Helpers\CategoryColorHelper::getAllCategoryColors($categoryExpense['labels']);
                    @endphp
                    {{-- <div class="mt-3">
                        <table class="table table-sm">
                            <tbody>
                                @foreach($categoryExpense['labels'] as $index => $label)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td class="text-end">{{ $categoryExpense['data'][$index] }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Report Section (new layout: left cards + right total panel) -->

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i> Laporan Pengeluaran Berdasarkan Kategori
            </h5>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">Lihat Laporan Lengkap</a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="row category-grid g-3">
                    @php
                        $categoryColors = \App\Helpers\CategoryColorHelper::getAllCategoryColors(array_keys($expenseReport['report']));
                    @endphp
                    @foreach($expenseReport['report'] as $category => $data)
                        @php $colors = $categoryColors[$category] ?? ['badge' => '#6c757d', 'progress' => '#0d6efd']; @endphp
                        <div class="col-12 col-md-6">
                            <div class="card category-card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="category-title">{{ $category }}</div>
                                            <div class="category-sub">{{ $data['count'] }} Transaksi</div>
                                        </div>
                                        <div>
                                            <span class="badge text-white" style="background-color: {{ $colors['badge'] }};">{{ $data['percentage'] }}%</span>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="category-amount">Rp {{ number_format($data['total'],0,',','.') }}</div>
                                    </div>

                                    <div class="mt-2">
                                            <div class="progress progress-sm bg-light">
                                            <div class="progress-bar" role="progressbar" data-percent="{{ $data['percentage'] }}" style="width:0; background-color: {{ $colors['progress'] }};" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0">
                    <div class="card-body p-0">
                        <div class="total-panel total-gradient">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="total-icon">
                                            <i class="bi bi-wallet2 fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="text-white-50">Total Keseluruhan</div>
                                            <div class="h5 mb-0 fw-bold">GRAND TOTAL</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <div class="display-6 fw-bold" id="grandTotal" data-target="{{ $expenseReport['grandTotal'] }}">Rp 0</div>
                                </div>
                            </div>

                            <div>
                                <div class="mt-3">
                                    <small class="text-white-50">Progress Allocation</small>
                                    <div class="progress mt-2" style="height:8px; border-radius:8px;">
                                        <div class="progress-bar bg-white" role="progressbar" data-percent="100" style="width:0; opacity:.95;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('reports.index') }}" class="btn btn-light btn-block w-100">View Detailed Report</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- (Removed duplicate Charts Section — charts are defined above) -->

    <!-- Chart Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const submissionChartData = @json($submissionChart);
            const categoryExpenseData = @json($categoryExpense);
            @php
                $donutColors = [];
                $donutBorders = [];
                foreach($categoryExpense['labels'] as $label) {
                    $c = $chartCategoryColors[$label] ?? ['badge' => '#6c757d', 'progress' => '#5c636a'];
                    $donutColors[] = $c['badge'];
                    $donutBorders[] = $c['progress'];
                }
            @endphp
            const donutColors = @json($donutColors);
            const donutBorders = @json($donutBorders);

            if (typeof window.initFinanceCharts === 'function') {
                window.initFinanceCharts(submissionChartData, categoryExpenseData, donutColors, donutBorders);
            }
            if (typeof window.initFinanceAnimations === 'function') {
                window.initFinanceAnimations();
            }
        });
    </script>
</x-app-layout>

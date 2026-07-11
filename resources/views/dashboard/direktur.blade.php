<x-app-layout>
    <x-slot name="header">Dashboard Direktur</x-slot>

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

    <div class="row g-4 mb-4">
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
                </div>
            </div>
        </div>
    </div>

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



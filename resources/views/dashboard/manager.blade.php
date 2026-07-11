<x-app-layout>
    <x-slot name="header">Dashboard Manager</x-slot>
    
    <!-- Summary Cards -->
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
                        <div class="summary-title text-muted">Menunggu Persetujuan Anda</div>
                        <div class="summary-value text-dark" data-target="{{ $menungguApproval }}">0</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
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
                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.initFinanceAnimations === 'function') {
                window.initFinanceAnimations();
            }
        });
    </script>
    @endpush
</x-app-layout>

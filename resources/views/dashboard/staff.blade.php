<x-app-layout>
    <x-slot name="header">Dashboard Staff</x-slot>

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
                        <div class="summary-title text-muted">Dalam Proses</div>
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

    <div class="mt-4 d-grid d-sm-flex">
        <a href="{{ route('submissions.create') }}" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill d-inline-flex align-items-center justify-content-center">
            <i class="bi bi-plus-circle me-2"></i> Buat Pengajuan Baru
        </a>
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

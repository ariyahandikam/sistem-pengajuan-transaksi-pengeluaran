<x-app-layout>
    <x-slot name="header">Admin Dashboard</x-slot>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-primary">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="me-3">
                        <div class="summary-title text-muted">Total Users</div>
                        <div class="summary-value text-dark" data-target="{{ \App\Models\User::count() }}">0</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-people-fill text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-info">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="me-3">
                        <div class="summary-title text-muted">Total Pengajuan</div>
                        <div class="summary-value text-dark" data-target="{{ \App\Models\Submission::count() }}">0</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-file-earmark-text text-info fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-warning">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="me-3">
                        <div class="summary-title text-muted">Total Pending</div>
                        <div class="summary-value text-dark" data-target="{{ \App\Models\Submission::whereNotIn('status', [\App\Models\Submission::STATUS_PAID, \App\Models\Submission::STATUS_REJECTED, \App\Models\Submission::STATUS_DRAFT])->count() }}">0</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-success">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="me-3">
                        <div class="summary-title text-muted">Total Approved</div>
                        <div class="summary-value text-dark" data-target="{{ \App\Models\Submission::where('status', \App\Models\Submission::STATUS_WAITING_FINANCE)->count() }}">0</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Content for Admin can go here -->

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

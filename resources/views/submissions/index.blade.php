<x-app-layout>

    <!-- Header & Action -->
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Daftar Pengajuan Saya</h4>
            <p class="text-muted small mb-0">Kelola dan pantau status pengajuan pengeluaran Anda</p>
        </div>
        <a href="{{ route('submissions.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Buat Pengajuan
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4 fade-in-up">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Cari No. Pengajuan atau Kategori...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="statusFilter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Submitted</option>
                        <option value="waiting_spv">Menunggu SPV</option>
                        <option value="waiting_manager">Menunggu Manager</option>
                        <option value="waiting_direktur">Menunggu Direktur</option>
                        <option value="waiting_finance">Menunggu Pembayaran</option>
                        <option value="paid">Paid</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary w-100" id="resetBtn">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4 fade-in-up">
        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-primary h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-title text-muted">Total Pengajuan</div>
                        <div class="summary-value fs-3 text-dark">{{ $submissions->total() }}</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-warning h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-title text-muted">Menunggu Approval</div>
                        <div class="summary-value fs-3 text-dark">{{ $submissions->whereNotIn('status', [\App\Models\Submission::STATUS_DRAFT, \App\Models\Submission::STATUS_PAID, \App\Models\Submission::STATUS_REJECTED])->count() }}</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-success h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-title text-muted">Paid</div>
                        <div class="summary-value fs-3 text-dark">{{ $submissions->where('status', \App\Models\Submission::STATUS_PAID)->count() }}</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card summary-card shadow-sm accent-danger h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="summary-title text-muted">Rejected</div>
                        <div class="summary-value fs-3 text-dark">{{ $submissions->where('status', \App\Models\Submission::STATUS_REJECTED)->count() }}</div>
                    </div>
                    <div class="summary-icon">
                        <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card fade-in-up">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0 submissions-table">
                    <thead>
                        <tr>
                            <th class="ps-4">No. Pengajuan</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                            <tr class="submission-row" data-status="{{ $submission->status }}">
                                <td class="ps-4 fw-medium text-primary">
                                    <i class="bi bi-file-earmark me-2"></i>{{ $submission->submission_number }}
                                </td>
                                <td>
                                    <span class="text-muted">{{ $submission->submission_date->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $submission->category->name }}</span>
                                </td>
                                <td>
                                    <strong class="text-dark">Rp {{ number_format($submission->amount, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $submission->status_badge }} bg-opacity-10 text-{{ $submission->status_badge }} border border-{{ $submission->status_badge }} border-opacity-25 px-2 py-1">
                                        {{ $submission->status_label }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('submissions.show', $submission) }}" class="btn btn-light border" data-bs-toggle="tooltip" title="Detail">
                                            <i class="bi bi-eye text-info"></i>
                                        </a>
                                        @if($submission->isEditable())
                                            <a href="{{ route('submissions.edit', $submission) }}" class="btn btn-light border" data-bs-toggle="tooltip" title="Edit">
                                                <i class="bi bi-pencil text-warning"></i>
                                            </a>
                                            <button class="btn btn-light border delete-btn" data-id="{{ $submission->id }}" data-number="{{ $submission->submission_number }}" data-bs-toggle="tooltip" title="Hapus">
                                                <i class="bi bi-trash text-danger"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        <p class="mb-0 fs-5">Belum ada data pengajuan</p>
                                        <small>Mulai buat pengajuan baru sekarang!</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($submissions->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $submissions->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger bg-opacity-10 border-0">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-0 fs-5 text-center">Hapus pengajuan <strong id="deleteSubmissionNumber"></strong>?</p>
                    <p class="text-muted small text-center mt-2 mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate rows
            const rows = document.querySelectorAll('.submission-row');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.animation = `fade-in-up 0.5s ease forwards ${index * 0.05}s`;
            });

            // Filter
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const resetBtn = document.getElementById('resetBtn');

            function filterTable() {
                const searchValue = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;

                rows.forEach(row => {
                    const submissionNumber = row.querySelector('td:first-child').textContent.toLowerCase();
                    const category = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const status = row.dataset.status;

                    const matchSearch = submissionNumber.includes(searchValue) || category.includes(searchValue);
                    const matchStatus = !statusValue || status === statusValue;

                    if (matchSearch && matchStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('keyup', filterTable);
            statusFilter.addEventListener('change', filterTable);

            resetBtn.addEventListener('click', function() {
                searchInput.value = '';
                statusFilter.value = '';
                rows.forEach(row => row.style.display = '');
            });

            // Delete Modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('deleteSubmissionNumber').textContent = this.dataset.number;
                    document.getElementById('deleteForm').action = `/submissions/${this.dataset.id}`;
                    deleteModal.show();
                });
            });
            
            // Add custom animation style dynamically if missing
            if (!document.querySelector('#row-animation')) {
                const style = document.createElement('style');
                style.id = 'row-animation';
                style.innerHTML = `@keyframes fade-in-up { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }`;
                document.head.appendChild(style);
            }
        });
    </script>
    @endpush
</x-app-layout>

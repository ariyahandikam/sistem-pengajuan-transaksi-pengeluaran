<x-app-layout>

    <!-- Header & Action -->
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                @if(Auth::user()->roleSlug === 'finance')
                    <i class="bi bi-wallet2 text-primary me-2"></i>Menunggu Pembayaran
                @else
                    <i class="bi bi-check2-square text-primary me-2"></i>Menunggu Persetujuan Anda
                @endif
            </h4>
            <p class="text-muted small mb-0">Daftar pengajuan yang perlu diproses oleh Anda</p>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card fade-in-up shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0 submissions-table">
                    <thead>
                        <tr>
                            <th class="ps-4">No. Pengajuan</th>
                            <th>Tanggal</th>
                            <th>Pengaju</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                            <tr class="submission-row">
                                <td class="ps-4 fw-medium text-primary">
                                    <i class="bi bi-file-earmark me-2"></i>{{ $submission->submission_number }}
                                </td>
                                <td>
                                    <span class="text-muted">{{ $submission->submission_date->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar bg-primary bg-opacity-10 text-primary me-2" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                            {{ strtoupper(substr($submission->user->name, 0, 1)) }}
                                        </div>
                                        <span class="fw-medium">{{ $submission->user->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $submission->category->name }}</span>
                                </td>
                                <td>
                                    <strong class="text-dark">Rp {{ number_format($submission->amount, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('approvals.show', $submission) }}" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill">
                                        Proses <i class="bi bi-arrow-right-short ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-check-circle fs-1 d-block mb-3 text-success"></i>
                                        <p class="mb-0 fs-5">Semua Beres!</p>
                                        <small>Tidak ada pengajuan yang menunggu diproses.</small>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate rows
            const rows = document.querySelectorAll('.submission-row');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.animation = `fade-in-up 0.5s ease forwards ${index * 0.05}s`;
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

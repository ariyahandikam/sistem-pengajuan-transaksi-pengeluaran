<x-app-layout>

    <!-- Header & Action -->
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Persetujuan
            </h4>
            <p class="text-muted small mb-0">Riwayat pengajuan yang sudah Anda proses</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4 fade-in-up shadow-sm border-0">
        <div class="card-body">
            <form method="get" class="row gx-2 gy-2 align-items-end">
                <div class="col-12 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="search" name="q" value="{{ request('q') }}" class="form-control border-start-0 ps-0" placeholder="Cari No. Pengajuan...">
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @if(Auth::user()->roleSlug === 'finance')
                            <option value="paid" {{ request('status')=='paid' ? 'selected' : '' }}>Paid</option>
                            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                        @elseif(Auth::user()->roleSlug === 'direktur')
                            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="paid" {{ request('status')=='paid' ? 'selected' : '' }}>Paid</option>
                        @else
                            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                        @endif
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-2">
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-2">
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control" title="Dari Tanggal">
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control" title="Sampai Tanggal">
                </div>
                <div class="col-auto ms-auto d-flex gap-1 align-items-center justify-content-end">
                    <button type="submit" class="btn btn-primary" title="Filter"><i class="bi bi-funnel"></i></button>
                    <a href="{{ route('approvals.history.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-clockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card fade-in-up shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0 submissions-table">
                    <thead>
                        <tr>
                            <th class="ps-4">No. Pengajuan</th>
                            <th>Pengaju</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal Proses</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvals as $approval)
                            <tr class="submission-row">
                                <td class="ps-4 fw-medium text-primary">
                                    <i class="bi bi-file-earmark me-2"></i>{{ $approval->submission->submission_number ?? '-' }}
                                </td>
                                <td>
                                    @if($approval->submission && $approval->submission->user)
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar bg-primary bg-opacity-10 text-primary me-2" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                                {{ strtoupper(substr($approval->submission->user->name, 0, 1)) }}
                                            </div>
                                            <span class="fw-medium">{{ $approval->submission->user->name }}</span>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $approval->submission->category->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <strong class="text-dark">Rp {{ number_format($approval->submission->amount ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @php
                                        $actorName = $approval->user->name ?? 'System';
                                        $roleLabel = $approval->role ? ucfirst($approval->role) : 'Unknown';
                                    @endphp
                                    @if($approval->status == 'approved')
                                        @php
                                            $isFinancePaid = ($approval->role === 'finance') && optional($approval->submission)->status === \App\Models\Submission::STATUS_PAID;
                                        @endphp
                                        @if($isFinancePaid)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Paid</span>
                                            <div class="text-muted small mt-1">oleh {{ $actorName }} ({{ $roleLabel }})</div>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Approved</span>
                                            <div class="text-muted small mt-1">oleh {{ $actorName }} ({{ $roleLabel }})</div>
                                        @endif
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Rejected</span>
                                        <div class="text-muted small mt-1">oleh {{ $actorName }} ({{ $roleLabel }})</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted"><i class="bi bi-calendar-check me-1"></i>{{ optional($approval->approved_at)->format('d/m/Y H:i') ?? '-' }}</span>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('approvals.history.show', $approval->submission_id) }}" class="btn btn-light border btn-sm px-3 shadow-sm rounded-pill" data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="bi bi-eye text-primary"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-clock-history fs-1 d-block mb-3"></i>
                                        <p class="mb-0 fs-5">Belum ada riwayat</p>
                                        <small>Anda belum memproses pengajuan apapun.</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($approvals->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $approvals->links('pagination::bootstrap-5') }}
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

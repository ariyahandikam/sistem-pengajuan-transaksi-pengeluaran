<x-app-layout>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="bi bi-clock-history text-primary me-2"></i>Log Aktivitas
            </h4>
            <p class="text-muted small mb-0">Catatan riwayat aktivitas pengguna dalam sistem</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4 fade-in-up">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Cari aktivitas atau pengguna...">
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control" title="Dari Tanggal">
                </div>
                <div class="col-md-3">
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control" title="Sampai Tanggal">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1" type="submit">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.activitylogs.index') }}" class="btn btn-outline-secondary" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm border-0 fade-in-up">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Waktu</th>
                            <th>Pengguna</th>
                            <th>Modul</th>
                            <th>Aktivitas</th>
                            <th>IP Address</th>
                            <th class="text-center pe-4">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $act)
                            <tr>
                                <td class="ps-4 fw-medium text-muted align-middle">
                                    {{ $act->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="align-middle">
                                    <div class="fw-bold text-dark">{{ $act->causer?->name ?? 'System' }}</div>
                                    @if (! empty($act->causer_role_label))
                                        <div class="{{ $act->causer_role_class }} mt-1" style="font-size: 0.7rem; padding: 2px 6px;">{{ $act->causer_role_label }}</div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-light text-dark border">
                                        {{ $act->properties['module'] ?? class_basename($act->subject_type ?? $act->log_name) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <div class="text-dark fw-medium">{{ $act->display_title ?? $act->description }}</div>
                                    <div class="text-muted small text-truncate" style="max-width: 200px;">
                                        {{ $act->display_description ?? '-' }}
                                    </div>
                                </td>
                                <td class="align-middle text-muted small">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $act->properties['ip'] ?? request()->ip() }}
                                </td>
                                <td class="text-center align-middle pe-4">
                                    <a href="{{ route('admin.activitylogs.show', $act) }}" class="btn btn-light border btn-sm shadow-sm rounded-pill px-3 text-primary">
                                        <i class="bi bi-eye me-1"></i>Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                                    Tidak ada data log aktivitas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($activities->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $activities->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</x-app-layout>

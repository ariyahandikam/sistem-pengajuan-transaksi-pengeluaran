<x-app-layout>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="bi bi-shield-check text-primary me-2"></i>Audit Trail
            </h4>
            <p class="text-muted small mb-0">Lacak seluruh jejak perubahan data dan aktivitas pengguna dalam sistem</p>
        </div>
        <a href="{{ route('admin.audit-trail.export', request()->query()) }}" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
        </a>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4 fade-in-up">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-activity fs-4 text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Aktivitas</div>
                        <h5 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 bg-success bg-opacity-10 p-3 me-3">
                        <i class="bi bi-plus-circle fs-4 text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Data Dibuat</div>
                        <h5 class="fw-bold mb-0">{{ number_format($stats['creates']) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 bg-warning bg-opacity-10 p-3 me-3">
                        <i class="bi bi-pencil-square fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Data Diubah</div>
                        <h5 class="fw-bold mb-0">{{ number_format($stats['updates']) }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 bg-danger bg-opacity-10 p-3 me-3">
                        <i class="bi bi-trash3 fs-4 text-danger"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Data Dihapus</div>
                        <h5 class="fw-bold mb-0">{{ number_format($stats['deletes']) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filter Card -->
    <div class="card shadow-sm border-0 mb-4 fade-in-up">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="m-0 fw-bold">
                <i class="bi bi-funnel me-2 text-muted"></i>Filter & Pencarian
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.audit-trail.index') }}">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label small text-muted fw-medium">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="search" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Cari pengguna, deskripsi, data...">
                        </div>
                    </div>

                    <!-- Module -->
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label small text-muted fw-medium">Modul</label>
                        <select name="module" class="form-select">
                            <option value="">Semua Modul</option>
                            @foreach($modules as $key => $label)
                                <option value="{{ $key }}" {{ request('module') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action -->
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label small text-muted fw-medium">Aksi</label>
                        <select name="action" class="form-select">
                            <option value="">Semua Aksi</option>
                            @foreach($actions as $key => $label)
                                <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- User -->
                    <div class="col-md-4 col-lg-4">
                        <label class="form-label small text-muted fw-medium">Pengguna</label>
                        <select name="user_id" class="form-select">
                            <option value="">Semua Pengguna</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div class="col-md-3 col-lg-3">
                        <label class="form-label small text-muted fw-medium">Dari Tanggal</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                    </div>

                    <!-- Date To -->
                    <div class="col-md-3 col-lg-3">
                        <label class="form-label small text-muted fw-medium">Sampai Tanggal</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-2 col-lg-2 d-flex align-items-end gap-2">
                        <button class="btn btn-primary flex-grow-1" type="submit">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.audit-trail.index') }}" class="btn btn-outline-secondary" title="Reset semua filter">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Trail Timeline / Table -->
    <div class="card shadow-sm border-0 fade-in-up">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold">
                <i class="bi bi-list-ul me-2 text-muted"></i>Riwayat Jejak Audit
            </h6>
            <span class="badge bg-primary rounded-pill">{{ $audits->total() }} data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 160px;">Waktu</th>
                            <th style="width: 180px;">Pengguna</th>
                            <th style="width: 120px;">Modul</th>
                            <th style="width: 110px;">Aksi</th>
                            <th>Detail Perubahan</th>
                            <th class="text-center pe-4" style="width: 80px;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                            <tr>
                                <!-- Waktu -->
                                <td class="ps-4 align-middle">
                                    <div class="fw-medium text-dark small">{{ $audit->created_at->format('d/m/Y') }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $audit->created_at->format('H:i:s') }}</div>
                                </td>

                                <!-- Pengguna -->
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2 shadow-sm" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                            {{ strtoupper(substr($audit->causer?->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $audit->causer?->name ?? 'System' }}</div>
                                            @if($audit->causer_role)
                                                <span class="badge {{ match($audit->causer_role) {
                                                    'admin' => 'bg-dark',
                                                    'staff' => 'bg-secondary',
                                                    'spv' => 'bg-info text-dark',
                                                    'manager' => 'bg-warning text-dark',
                                                    'direktur' => 'bg-primary',
                                                    'finance' => 'bg-success',
                                                    default => 'bg-light text-dark'
                                                } }}" style="font-size: 0.65rem; padding: 2px 6px;">{{ strtoupper($audit->causer_role) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Modul -->
                                <td class="align-middle">
                                    <span class="badge bg-light text-dark border fw-medium" style="font-size: 0.75rem;">
                                        {{ $audit->display_module }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="align-middle">
                                    <span class="badge bg-{{ $audit->action_color }} bg-opacity-10 text-{{ $audit->action_color }}" style="font-size: 0.75rem;">
                                        {{ $audit->display_action }}
                                    </span>
                                </td>

                                <!-- Detail -->
                                <td class="align-middle">
                                    <div class="text-dark small text-truncate" style="max-width: 320px;" title="{{ $audit->display_detail }}">
                                        {{ $audit->display_detail }}
                                    </div>
                                </td>

                                <!-- Tombol Detail -->
                                <td class="text-center align-middle pe-4">
                                    <a href="{{ route('admin.audit-trail.show', $audit) }}" class="btn btn-light border btn-sm shadow-sm rounded-pill px-3 text-primary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-shield-x fs-1 d-block mb-3 text-muted"></i>
                                    <h6 class="fw-bold">Tidak ada data audit trail</h6>
                                    <p class="small text-muted">Coba ubah filter pencarian Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($audits->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $audits->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</x-app-layout>

<x-app-layout>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="mb-1 fw-bold text-dark">
                <i class="bi bi-people text-primary me-2"></i>Daftar Pengguna
            </h4>
            <p class="text-muted small mb-0">Manajemen akses dan data pengguna sistem</p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="bi bi-person-plus me-2"></i>Tambah Pengguna
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm border-0 fade-in-up">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role / Jabatan</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td class="ps-4 fw-medium text-dark">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar bg-primary bg-opacity-10 text-primary me-3">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $u->name }}</div>
                                            <small class="text-muted">ID: {{ $u->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-muted">{{ $u->email }}</td>
                                <td class="align-middle">
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                        <i class="bi bi-shield-check text-primary me-1"></i>{{ $u->role?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    @if(strtolower($u->status ?? 'active') === 'active')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Aktif</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle pe-4">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-light border text-primary" data-bs-toggle="tooltip" title="Edit Pengguna">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light border text-danger" data-bs-toggle="tooltip" title="Hapus Pengguna">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-3"></i>
                                    Tidak ada data pengguna.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
    @endpush
</x-app-layout>

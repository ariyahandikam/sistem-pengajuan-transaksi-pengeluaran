<x-app-layout>

    <div class="mb-3 fade-in-up">
        <a href="{{ route('admin.audit-trail.index') }}" class="btn btn-light border btn-sm shadow-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Audit Trail
        </a>
    </div>

    <div class="row g-4 fade-in-up">
        <!-- Info Utama -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold">
                        <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Aktivitas
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th class="text-muted ps-4 py-3" width="35%">ID</th>
                            <td class="py-3"><code>#{{ $activity->id }}</code></td>
                        </tr>
                        <tr class="border-top">
                            <th class="text-muted ps-4 py-3">Waktu</th>
                            <td class="py-3">
                                <div class="fw-medium">{{ $activity->created_at->format('d/m/Y H:i:s') }}</div>
                                <div class="text-muted small">{{ $activity->created_at->diffForHumans() }}</div>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <th class="text-muted ps-4 py-3">Pengguna</th>
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-2 shadow-sm" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                        {{ strtoupper(substr($activity->causer?->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $activity->causer?->name ?? 'System' }}</div>
                                        @if($activity->causer?->role)
                                            <span class="badge {{ match($activity->causer->role->slug) {
                                                'admin' => 'bg-dark',
                                                'staff' => 'bg-secondary',
                                                'spv' => 'bg-info text-dark',
                                                'manager' => 'bg-warning text-dark',
                                                'direktur' => 'bg-primary',
                                                'finance' => 'bg-success',
                                                default => 'bg-light text-dark'
                                            } }}" style="font-size: 0.65rem;">{{ strtoupper($activity->causer->role->slug) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <th class="text-muted ps-4 py-3">Modul</th>
                            <td class="py-3">
                                <span class="badge bg-light text-dark border fw-medium">{{ $activity->display_module }}</span>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <th class="text-muted ps-4 py-3">Aksi</th>
                            <td class="py-3">
                                <span class="badge bg-{{ $activity->action_color }}">{{ $activity->display_action }}</span>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <th class="text-muted ps-4 py-3">Detail</th>
                            <td class="py-3">{{ $activity->display_detail }}</td>
                        </tr>
                        <tr class="border-top">
                            <th class="text-muted ps-4 py-3">IP Address</th>
                            <td class="py-3">
                                <code>{{ data_get($activity->properties, 'ip', '-') }}</code>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <th class="text-muted ps-4 py-3">Subject</th>
                            <td class="py-3">
                                <code class="small">{{ class_basename($activity->subject_type ?? '-') }} #{{ $activity->subject_id ?? '-' }}</code>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Perubahan Data (Diff) -->
        <div class="col-lg-7">
            @if(count($changes) > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 fw-bold">
                            <i class="bi bi-arrow-left-right me-2 text-warning"></i>Perubahan Data
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Field</th>
                                        <th>Nilai Sebelumnya</th>
                                        <th>Nilai Baru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($changes as $change)
                                        <tr class="{{ $change['changed'] ? '' : 'table-light' }}">
                                            <td class="ps-4 fw-medium text-dark">
                                                {{ $change['field'] }}
                                                @if($change['changed'])
                                                    <i class="bi bi-dot text-warning"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if($change['old'] && $change['old'] !== '-')
                                                    <span class="{{ $change['changed'] ? 'text-danger' : 'text-muted' }}">
                                                        <i class="bi bi-dash-circle me-1" style="font-size: 0.7rem;"></i>{{ $change['old'] }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($change['new'] && $change['new'] !== '-')
                                                    <span class="{{ $change['changed'] ? 'text-success fw-medium' : 'text-muted' }}">
                                                        <i class="bi bi-plus-circle me-1" style="font-size: 0.7rem;"></i>{{ $change['new'] }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-clipboard-data fs-1 text-muted d-block mb-3"></i>
                        <h6 class="fw-bold text-muted">Tidak ada data perubahan terdeteksi</h6>
                        <p class="text-muted small">Aktivitas ini tidak menyimpan perbandingan data sebelum/sesudah.</p>
                    </div>
                </div>
            @endif

            <!-- Raw JSON Properties -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold">
                        <i class="bi bi-code-slash me-2 text-info"></i>Raw Properties (JSON)
                    </h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light rounded p-3 mb-0" style="font-size: 0.8rem; max-height: 400px; overflow: auto;"><code>{{ json_encode($activity->properties->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

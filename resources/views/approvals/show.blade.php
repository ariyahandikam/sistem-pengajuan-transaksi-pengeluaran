<x-app-layout>

    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Pengajuan</h6>
                    <span class="badge bg-{{ $submission->status_badge }} px-3 py-2 rounded-pill">
                        {{ $submission->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%" class="text-muted">Pengaju</th>
                            <td class="fw-bold">{{ $submission->user->name }} <span class="badge bg-light text-dark ms-1">{{ $submission->user->roleSlug }}</span></td>
                        </tr>
                        <tr>
                            <th class="text-muted">No. Pengajuan</th>
                            <td>{{ $submission->submission_number }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Tanggal</th>
                            <td>{{ $submission->submission_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Kategori</th>
                            <td>{{ $submission->category->name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Nominal</th>
                            <td class="fs-5 fw-bold text-success">Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Keterangan</th>
                            <td>{{ $submission->description }}</td>
                        </tr>
                        @if($submission->attachment && is_array($submission->attachment) && count($submission->attachment) > 0)
                            <tr>
                                <th class="text-muted">Lampiran</th>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($submission->attachment as $index => $path)
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('submissions.view', ['submission' => $submission->id, 'index' => $index]) }}" class="btn btn-sm btn-outline-info" target="_blank" title="Lihat File">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
                                                <a href="{{ route('submissions.download', ['submission' => $submission->id, 'index' => $index]) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Download File">
                                                    <i class="bi bi-download"></i> File {{ $index + 1 }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Riwayat Persetujuan -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-info">Riwayat Persetujuan Sebelumnya</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($submission->approvals as $approval)
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold">{{ ucfirst($approval->role) }}</span>
                                    @if($approval->isApproved())
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </div>
                                <div class="text-muted small mb-2">
                                    Oleh: {{ $approval->user->name ?? 'System' }}<br>
                                    Waktu: {{ $approval->approved_at->format('d/m/Y H:i') }}
                                </div>
                                @if($approval->notes)
                                    <div class="bg-light p-2 rounded small text-dark">
                                        <em>Catatan:</em> {{ $approval->notes }}
                                    </div>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-3">Belum ada riwayat persetujuan.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        @if(Auth::user()->roleSlug === 'finance') Form Pembayaran @else Form Persetujuan @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if(Auth::user()->roleSlug === 'finance')
                        @php
                            $budget = \App\Models\Budget::where('category_id', $submission->category_id)
                                ->where('year', $submission->submission_date->year)
                                ->first();
                            
                            $budgetStatus = null;
                            $remainingBudget = 0;
                            $insufficientBudget = false;
                            
                            if ($budget) {
                                $remainingBudget = $budget->total_budget - $budget->used_budget;
                                $insufficientBudget = $submission->amount > $remainingBudget;
                                $budgetStatus = [
                                    'total' => $budget->total_budget,
                                    'used' => $budget->used_budget,
                                    'remaining' => $remainingBudget,
                                    'insufficient' => $insufficientBudget
                                ];
                            }
                        @endphp

                        <!-- Budget Status Card -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-muted mb-3">
                                <i class="bi bi-wallet2 me-1"></i> Status Anggaran Kategori
                            </h6>
                            @if($budgetStatus)
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="bg-light p-3 rounded text-center">
                                            <small class="text-muted d-block">Total Anggaran</small>
                                            <h6 class="fw-bold text-dark mb-0">Rp {{ number_format($budgetStatus['total'], 0, ',', '.') }}</h6>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-light p-3 rounded text-center">
                                            <small class="text-muted d-block">Terpakai</small>
                                            <h6 class="fw-bold text-danger mb-0">Rp {{ number_format($budgetStatus['used'], 0, ',', '.') }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-light p-3 rounded text-center mb-3">
                                    <small class="text-muted d-block">Sisa Anggaran</small>
                                    <h6 class="fw-bold {{ $budgetStatus['remaining'] >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                        Rp {{ number_format($budgetStatus['remaining'], 0, ',', '.') }}
                                    </h6>
                                </div>

                                @if($budgetStatus['insufficient'])
                                    <div class="alert alert-danger mb-0" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <strong>⚠️ Peringatan: Saldo Tidak Cukup!</strong>
                                        <div class="small mt-2">
                                            Nominal pengajuan <strong>Rp {{ number_format($submission->amount, 0, ',', '.') }}</strong> 
                                            melebihi sisa anggaran kategori <strong>"{{ $submission->category->name }}"</strong>.
                                            <div class="mt-2">
                                                Kurang: <strong class="text-danger">Rp {{ number_format($submission->amount - $budgetStatus['remaining'], 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-success mb-0" role="alert">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <strong>✓ Saldo Mencukupi</strong>
                                        <div class="small mt-1">
                                            Sisa setelah pengajuan ini: <strong>Rp {{ number_format($budgetStatus['remaining'] - $submission->amount, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning mb-0" role="alert">
                                    <i class="bi bi-exclamation-circle me-2"></i>
                                    <strong>ℹ️ Anggaran Belum Ditetapkan</strong>
                                    <div class="small mt-1">
                                        Anggaran untuk kategori "{{ $submission->category->name }}" tahun {{ $submission->submission_date->year }} belum ada di sistem.
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('approvals.process', $submission) }}" method="POST">
                        @csrf

                        @if(Auth::user()->roleSlug === 'finance')
                            <div class="mb-3">
                                <label for="payment_method" class="form-label fw-bold">Metode Pembayaran</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="cash">Cash</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="reference_number" class="form-label fw-bold">Nomor Referensi / Bukti Transfer (Opsional)</label>
                                <input type="text" name="reference_number" id="reference_number" class="form-control">
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold">Catatan (Opsional)</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Tuliskan catatan tambahan..."></textarea>
                        </div>

                        <hr class="mb-4">

                        <input type="hidden" name="action" id="approvalActionInput" value="">

                        <div class="d-grid gap-2">
                            @php
                                $approveButtonDisabled = false;
                                $approveButtonTooltip = '';
                                
                                if (Auth::user()->roleSlug === 'finance' && isset($budgetStatus)) {
                                    if (!$budgetStatus) {
                                        $approveButtonDisabled = true;
                                        $approveButtonTooltip = 'Anggaran belum ditetapkan';
                                    } elseif ($budgetStatus['insufficient']) {
                                        $approveButtonDisabled = true;
                                        $approveButtonTooltip = 'Saldo anggaran tidak cukup';
                                    }
                                }
                            @endphp
                            
                            <button type="button" class="btn btn-success btn-lg approval-action-btn" data-action="approve" data-title="Konfirmasi Persetujuan" data-message="Apakah Anda yakin ingin menyetujui pengajuan ini?" data-confirm-text="Ya, Setujui" data-confirm-class="btn-success" {{ $approveButtonDisabled ? 'disabled' : '' }} @if($approveButtonDisabled) title="{{ $approveButtonTooltip }}" @endif>
                                @if(Auth::user()->roleSlug === 'finance')
                                    <i class="bi bi-check-circle me-1"></i> Proses Pembayaran
                                @else
                                    <i class="bi bi-check-circle me-1"></i> Setujui Pengajuan
                                @endif
                            </button>
                            <button type="button" class="btn btn-danger btn-lg approval-action-btn" data-action="reject" data-title="Konfirmasi Penolakan" data-message="Apakah Anda yakin ingin menolak pengajuan ini?" data-confirm-text="Ya, Tolak" data-confirm-class="btn-danger">
                                <i class="bi bi-x-circle me-1"></i> Tolak Pengajuan
                            </button>
                            <a href="{{ route('approvals.index') }}" class="btn btn-light mt-2 border">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="approvalConfirmModal" tabindex="-1" aria-labelledby="approvalConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold" id="approvalConfirmModalLabel">
                        <i class="bi bi-exclamation-circle me-2 text-primary"></i>
                        Konfirmasi Tindakan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2" id="approvalConfirmModalMessage">Apakah Anda yakin ingin melanjutkan?</p>
                    <p class="text-muted small mb-0">Tindakan ini akan diproses segera dan tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="approvalConfirmSubmitBtn">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const approvalActionButtons = document.querySelectorAll('.approval-action-btn');
            const approvalActionInput = document.getElementById('approvalActionInput');
            const approvalConfirmModalEl = document.getElementById('approvalConfirmModal');
            const approvalConfirmModal = new bootstrap.Modal(approvalConfirmModalEl);
            const approvalConfirmModalLabel = document.getElementById('approvalConfirmModalLabel');
            const approvalConfirmModalMessage = document.getElementById('approvalConfirmModalMessage');
            const approvalConfirmSubmitBtn = document.getElementById('approvalConfirmSubmitBtn');
            const approvalForm = document.querySelector('form[action="{{ route('approvals.process', $submission) }}"]');

            approvalActionButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const action = this.dataset.action;
                    const title = this.dataset.title;
                    const message = this.dataset.message;
                    const confirmText = this.dataset.confirmText;
                    const confirmClass = this.dataset.confirmClass || 'btn-primary';

                    approvalActionInput.value = action;
                    approvalConfirmModalLabel.textContent = title;
                    approvalConfirmModalMessage.textContent = message;
                    approvalConfirmSubmitBtn.textContent = confirmText;
                    approvalConfirmSubmitBtn.className = 'btn ' + confirmClass;
                    approvalConfirmSubmitBtn.dataset.action = action;

                    approvalConfirmModal.show();
                });
            });

            approvalConfirmSubmitBtn.addEventListener('click', function () {
                if (!approvalActionInput.value) {
                    return;
                }
                approvalForm.submit();
            });
        });
    </script>
</x-app-layout>

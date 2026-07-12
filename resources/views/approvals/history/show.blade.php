<x-app-layout>

    <div class="mb-3">
        <a href="{{ route('approvals.history.index') }}" class="btn btn-light btn-sm">&larr; Kembali</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Informasi Pengajuan</h5>
                    <dl class="row">
                        <dt class="col-sm-4">Nomor Pengajuan</dt>
                        <dd class="col-sm-8">{{ $submission->number }}</dd>

                        <dt class="col-sm-4">Nama Pengaju</dt>
                        <dd class="col-sm-8">{{ $submission->user->name }}</dd>

                        <dt class="col-sm-4">Kategori</dt>
                        <dd class="col-sm-8">{{ $submission->category->name ?? '-' }}</dd>

                        <dt class="col-sm-4">Nominal</dt>
                        <dd class="col-sm-8">Rp {{ number_format($submission->amount ?? 0,0,',','.') }}</dd>

                        <dt class="col-sm-4">Status Akhir</dt>
                        <dd class="col-sm-8">{{ ucfirst($submission->status) }}</dd>

                        <dt class="col-sm-4">Keterangan</dt>
                        <dd class="col-sm-8">{{ $submission->notes ?? '-' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <h5>Timeline Persetujuan</h5>
                    <ul class="list-unstyled">
                        @foreach($approvals as $ap)
                            <li class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ ucfirst($ap->role) }}:</strong> {{ $ap->user->name ?? 'System' }}
                                        @php
                                            $statusLabel = 'Rejected';
                                            if ($ap->status === 'approved') {
                                                $isFinancePaid = ($ap->role === 'finance') && optional($ap->submission)->status === \App\Models\Submission::STATUS_PAID;
                                                $statusLabel = $isFinancePaid ? 'Paid' : 'Approved';
                                            }
                                        @endphp
                                        <div class="text-muted">{{ $statusLabel }}</div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ optional($ap->approved_at)->format('Y-m-d H:i') ?? '-' }}</small>
                                    </div>
                                </div>
                                @if($ap->notes)
                                    <div class="mt-2"><small>Alasan/Tanggapan: {{ $ap->notes }}</small></div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>File Lampiran</h6>
                    @if($submission->attachment && is_array($submission->attachment) && count($submission->attachment) > 0)
                        <div class="d-flex flex-column gap-2 mt-2">
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
                    @else
                        <div class="text-muted mt-2">Tidak ada lampiran</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

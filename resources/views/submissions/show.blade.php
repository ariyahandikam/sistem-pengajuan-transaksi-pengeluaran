<x-app-layout>

    <div class="container-fluid py-2">
        <!-- Header with Status -->
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-file-earmark text-primary fs-4"></i>
                    </div>
                    <div>
                        <h2 class="h3 fw-bold mb-0">{{ $submission->submission_number }}</h2>
                        <p class="text-muted small mb-0">Dibuat pada {{ $submission->submission_date->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-{{ $submission->status_badge }} px-4 py-3 rounded-pill fs-6 status-badge">
                    <i class="bi bi-info-circle me-2"></i>{{ $submission->status_label }}
                </span>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Submission Details Card -->
                <div class="card border-0 shadow-sm mb-4 submission-detail-card">
                    <div class="card-header bg-gradient border-0 py-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Pengajuan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted small fw-bold text-uppercase">Nomor Pengajuan</label>
                                    <p class="fs-5 fw-bold text-primary mb-0">{{ $submission->submission_number }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted small fw-bold text-uppercase">Tanggal Pengajuan</label>
                                    <p class="fs-5 fw-bold mb-0">{{ $submission->submission_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted small fw-bold text-uppercase">Kategori</label>
                                    <p class="fs-5 fw-bold mb-0">
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                            <i class="bi bi-tag me-2"></i>{{ $submission->category->name }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted small fw-bold text-uppercase">Nominal</label>
                                    <p class="fs-4 fw-bold text-success mb-0">
                                        Rp {{ number_format($submission->amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-group">
                                    <label class="text-muted small fw-bold text-uppercase">Keterangan</label>
                                    <div class="bg-light p-3 rounded-3 mt-2">
                                        <p class="mb-0 text-dark">{{ $submission->description }}</p>
                                    </div>
                                </div>
                            </div>
                            @if($submission->attachment && is_array($submission->attachment) && count($submission->attachment) > 0)
                                <div class="col-12">
                                    <div class="info-group">
                                        <label class="text-muted small fw-bold text-uppercase">Lampiran</label>
                                        <div class="mt-2 d-flex flex-wrap gap-2">
                                            @foreach($submission->attachment as $index => $path)
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('submissions.view', ['submission' => $submission->id, 'index' => $index]) }}" class="btn btn-outline-info" target="_blank" title="Lihat File">
                                                        <i class="bi bi-eye"></i> Lihat
                                                    </a>
                                                    <a href="{{ route('submissions.download', ['submission' => $submission->id, 'index' => $index]) }}" class="btn btn-outline-primary" target="_blank" title="Download File">
                                                        <i class="bi bi-download"></i> File {{ $index + 1 }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Approval History Timeline -->
                <div class="card border-0 shadow-sm approval-timeline-card">
                    <div class="card-header bg-gradient border-0 py-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-clock-history me-2 text-info"></i>Riwayat Persetujuan
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="timeline-container p-4">
                            @forelse($submission->approvals as $index => $approval)
                                <div class="timeline-item mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="d-flex gap-3">
                                        <!-- Timeline Dot -->
                                        <div class="timeline-dot">
                                            @if($approval->isApproved())
                                                <div class="badge bg-success p-3">
                                                    <i class="bi bi-check-lg"></i>
                                                </div>
                                            @else
                                                <div class="badge bg-danger p-3">
                                                    <i class="bi bi-x-lg"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Timeline Content -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="fw-bold mb-1">{{ ucfirst($approval->role) }}</h6>
                                                    <small class="text-muted">
                                                        Oleh: <strong>{{ $approval->user->name ?? 'System' }}</strong>
                                                    </small>
                                                </div>
                                                @php
                                                    if ($approval->isApproved()) {
                                                        $isFinancePaid = ($approval->role === 'finance') && $submission->status === \App\Models\Submission::STATUS_PAID;
                                                        $label = $isFinancePaid ? 'Paid' : 'Approved';
                                                        $badge = 'bg-success';
                                                    } else {
                                                        $label = 'Rejected';
                                                        $badge = 'bg-danger';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badge }}">{{ $label }}</span>
                                            </div>
                                            <small class="text-muted d-block mb-2">
                                                <i class="bi bi-calendar-event me-1"></i>{{ $approval->approved_at->format('d/m/Y H:i') }}
                                            </small>
                                            @if($approval->notes)
                                                <div class="alert alert-light border-start border-4 {{ $approval->isApproved() ? 'border-success' : 'border-danger' }} mt-2 mb-0 p-3">
                                                    <strong class="d-block mb-1">Catatan:</strong>
                                                    <p class="mb-0 text-dark">{{ $approval->notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Belum ada riwayat persetujuan</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status Summary -->
                <div class="card border-0 shadow-sm mb-4 status-summary-card">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-bar-chart me-2 text-primary"></i>Status Saat Ini
                        </h6>
                        <div class="status-indicator mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Progress Persetujuan</small>
                                <small class="fw-bold">{{ $submission->approvals->where('status', 'approved')->count() }}/{{ $submission->approvals->count() }}</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                @php
                                    $totalApprovals = $submission->approvals->count();
                                    $approvedCount = $submission->approvals->where('status', 'approved')->count();
                                    $percentage = $totalApprovals > 0 ? ($approvedCount / $totalApprovals * 100) : 0;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div class="status-text mt-3">
                            @switch($submission->status)
                                @case('draft')
                                    <div class="alert alert-warning alert-sm" role="alert">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Draft</strong> - Belum disubmit. Anda masih bisa mengedit.
                                    </div>
                                    @break
                                @case('pending')
                                    <div class="alert alert-info alert-sm" role="alert">
                                        <i class="bi bi-hourglass-split me-2"></i>
                                        <strong>Menunggu</strong> - Sedang dalam proses persetujuan.
                                    </div>
                                    @break
                                @case('approved')
                                    <div class="alert alert-success alert-sm" role="alert">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <strong>Approved</strong> - The submission has been fully approved.
                                    </div>
                                    @break
                                @case('rejected')
                                    <div class="alert alert-danger alert-sm" role="alert">
                                        <i class="bi bi-x-circle me-2"></i>
                                        <strong>Rejected</strong> - The submission was rejected. Contact your supervisor.
                                    </div>
                                    @break
                                @case('paid')
                                    <div class="alert alert-success alert-sm" role="alert">
                                        <i class="bi bi-cash-coin me-2"></i>
                                        <strong>Paid</strong> - The submission has been paid.
                                    </div>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if($submission->isEditable())
                    <div class="d-grid gap-2 mb-4">
                        <a href="{{ route('submissions.edit', $submission) }}" class="btn btn-warning btn-lg">
                            <i class="bi bi-pencil me-2"></i>Edit Pengajuan
                        </a>
                    </div>
                @endif

                <div class="d-grid gap-2 mb-4">
                    @if(Auth::user()->hasRole('staff'))
                        <a href="{{ route('submissions.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                        </a>
                    @elseif(in_array(Auth::user()->roleSlug, ['spv', 'manager', 'direktur', 'finance']))
                        <a href="{{ route('approvals.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    @endif
                </div>

                <!-- Quick Info -->
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-lightbulb me-2 text-warning"></i>Informasi Cepat
                        </h6>
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="bi bi-bookmark me-2 text-primary"></i>
                                <strong>ID Pengajuan:</strong>
                                <br>
                                <code class="bg-white px-2 py-1 rounded">{{ $submission->id }}</code>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-calendar me-2 text-primary"></i>
                                <strong>Dibuat:</strong>
                                <br>{{ $submission->created_at->format('d/m/Y H:i') }}
                            </li>
                            <li>
                                <i class="bi bi-person me-2 text-primary"></i>
                                <strong>Pemohon:</strong>
                                <br>{{ Auth::user()->name }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .info-group {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .info-group:hover {
            background: #e9ecef;
        }

        .alert-sm {
            padding: 0.75rem;
            margin-bottom: 0;
            border-radius: 0.5rem;
        }

        .timeline-container {
            position: relative;
        }

        .timeline-item {
            position: relative;
            padding-left: 0;
        }

        .timeline-dot {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 50px;
            height: 50px;
            flex-shrink: 0;
        }

        .timeline-dot .badge {
            font-size: 1.1rem;
        }

        .submission-detail-card,
        .approval-timeline-card,
        .status-summary-card {
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .status-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        .progress {
            border-radius: 1rem;
            background-color: #e9ecef;
        }

        code {
            color: #e83e8c;
            background: white !important;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }

        .badge {
            font-weight: 600;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to status changes
            const statusBadge = document.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.style.animation = 'pulse 2s infinite';
            }

            // Timeline animation
            const timelineItems = document.querySelectorAll('.timeline-item');
            timelineItems.forEach((item, index) => {
                item.style.animation = `fadeInUp 0.5s ease ${index * 0.1}s both`;
            });
        });

        // Add CSS animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</x-app-layout>

<x-app-layout>

    <div class="container py-2">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-file-earmark-plus text-primary fs-4"></i>
                        </div>
                        <div>
                            <h2 class="h3 fw-bold mb-0">Buat Pengajuan Baru</h2>
                            <p class="text-muted small mb-0">Lengkapi form berikut untuk membuat pengajuan pengeluaran</p>
                        </div>
                    </div>
                </div>

                <!-- Main Form Card -->
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4">
                        <form id="submissionForm" action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="action" id="actionInput" value="draft">

                            <!-- Step 1: Category & Amount -->
                            <div class="form-section mb-4">
                                <h5 class="fw-bold mb-3 pb-2 border-bottom">
                                    <span class="badge bg-primary me-2">1</span>Informasi Dasar
                                </h5>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label fw-bold">
                                        <i class="bi bi-tag me-2 text-primary"></i>Kategori Pengeluaran
                                    </label>
                                    <select name="category_id" id="category_id" class="form-select form-select-lg @error('category_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback d-block">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="amount" class="form-label fw-bold">
                                        <i class="bi bi-cash-coin me-2 text-success"></i>Nominal Pengajuan (Rp)
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                                               value="{{ old('amount') }}" required min="1" step="1" placeholder="0">
                                        <span class="input-group-text bg-light" id="amountDisplay"></span>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle me-1"></i>Masukkan nominal yang sesuai dengan anggaran kategori
                                    </small>
                                    @error('amount')
                                        <div class="invalid-feedback d-block mt-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Step 2: Description -->
                            <div class="form-section mb-4">
                                <h5 class="fw-bold mb-3 pb-2 border-bottom">
                                    <span class="badge bg-primary me-2">2</span>Keterangan
                                </h5>

                                <div class="mb-3">
                                    <label for="description" class="form-label fw-bold">
                                        <i class="bi bi-pencil-square me-2 text-info"></i>Tujuan / Keterangan
                                    </label>
                                    <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" 
                                              placeholder="Jelaskan tujuan pengajuan ini secara detail..." required>{{ old('description') }}</textarea>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-muted">Berikan deskripsi yang jelas dan detail</small>
                                        <small class="text-muted"><span id="charCount">0</span>/500</small>
                                    </div>
                                    @error('description')
                                        <div class="invalid-feedback d-block">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Step 3: Attachment -->
                            <div class="form-section mb-4">
                                <h5 class="fw-bold mb-3 pb-2 border-bottom">
                                    <span class="badge bg-primary me-2">3</span>Lampiran
                                </h5>

                                <div class="mb-3">
                                    <label for="attachment" class="form-label fw-bold">
                                        <i class="bi bi-paperclip me-2 text-warning"></i>Upload Berkas Pendukung
                                    </label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="attachment[]" id="attachment" class="form-control @error('attachment') is-invalid @enderror" 
                                               accept=".pdf,.jpg,.jpeg,.png" style="display: none;" multiple>
                                        <div class="upload-area border-2 border-dashed rounded-3 p-5 text-center cursor-pointer" id="uploadArea">
                                            <i class="bi bi-cloud-arrow-up text-primary mb-3" style="font-size: 2rem;"></i>
                                            <p class="fw-bold mb-1">Klik atau Drag & Drop File</p>
                                            <small class="text-muted">Format: PDF, JPG, PNG | Max: 5MB/file | Maksimal 5 File</small>
                                        </div>
                                        <div id="filePreview" class="mt-3" style="display: none;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 fw-bold text-primary">File Terpilih:</h6>
                                                <button type="button" class="btn btn-sm btn-outline-danger" id="removeFile">Hapus Semua</button>
                                            </div>
                                            <div id="fileListContainer" class="d-flex flex-column gap-2"></div>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle me-1"></i>File lampiran bersifat opsional
                                    </small>
                                    @error('attachment')
                                        <div class="invalid-feedback d-block mt-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Summary Section -->
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">
                                        <i class="bi bi-clipboard-check me-2 text-success"></i>Ringkasan Pengajuan
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Kategori</small>
                                            <strong id="summaryCategory" class="text-primary">-</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Nominal</small>
                                            <strong id="summaryAmount" class="text-success">Rp 0</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 mt-5">
                                <button type="submit" name="action" value="draft" class="btn btn-outline-secondary btn-lg flex-grow-1">
                                    <i class="bi bi-save me-2"></i>Simpan Sebagai Draft
                                </button>
                                <button type="button" id="submitButton" class="btn btn-primary btn-lg flex-grow-1 submit-btn">
                                    <i class="bi bi-send-check me-2"></i>Submit Pengajuan
                                </button>
                                <a href="{{ route('submissions.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-x-lg me-2"></i>Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-4 border-0">
                    <div class="d-flex">
                        <i class="bi bi-info-circle-fill me-3 flex-shrink-0"></i>
                        <div>
                            <strong>Informasi Penting:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Pengajuan yang disimpan sebagai Draft dapat diedit kembali</li>
                                <li>Pengajuan yang disubmit akan masuk ke proses persetujuan</li>
                                <li>Anda akan menerima notifikasi saat ada update status</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="submitConfirmationModal" tabindex="-1" aria-labelledby="submitConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="submitConfirmationModalLabel">
                        <i class="bi bi-send-check me-2 text-primary"></i>Konfirmasi Submit Pengajuan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Apakah Anda yakin ingin mengirim pengajuan ini?</p>
                    <p class="text-muted small mb-0">Setelah disubmit, pengajuan akan masuk ke proses persetujuan dan tidak bisa diedit lagi.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmitBtn">Ya, Submit</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-section {
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-select-lg,
        .form-control.form-control-lg {
            padding: 0.875rem 1rem;
            font-size: 1rem;
            border-radius: 0.5rem;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .badge {
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
        }

        .upload-area {
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .upload-area:hover {
            background: #e9ecef;
            border-color: #0d6efd !important;
        }

        .upload-area.drag-over {
            background: rgba(13, 110, 253, 0.1);
            border-color: #0d6efd !important;
        }

        .input-group-text {
            border: 2px solid #e9ecef;
            font-weight: 600;
        }

        .card {
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .submit-btn {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            font-weight: 600;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.3) !important;
        }

        textarea {
            resize: vertical;
        }

        .input-group-lg > .input-group-text {
            padding: 0.875rem 1rem;
        }

        .form-label i {
            font-size: 1.1rem;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('submissionForm');
            const actionInput = document.getElementById('actionInput');
            const submitButton = document.getElementById('submitButton');
            const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
            const modalEl = document.getElementById('submitConfirmationModal');
            const categorySelect = document.getElementById('category_id');
            const amountInput = document.getElementById('amount');
            const descriptionInput = document.getElementById('description');
            const attachmentInput = document.getElementById('attachment');
            const uploadArea = document.getElementById('uploadArea');
            const filePreview = document.getElementById('filePreview');
            const removeFileBtn = document.getElementById('removeFile');
            const charCount = document.getElementById('charCount');

            // Format currency on amount input
            amountInput.addEventListener('input', function() {
                const value = this.value;
                document.getElementById('amountDisplay').textContent = value ? 'Rp ' + parseInt(value).toLocaleString('id-ID') : '';
                updateSummary();
            });

            // Update category in summary
            categorySelect.addEventListener('change', function() {
                const selectedText = this.options[this.selectedIndex].text;
                document.getElementById('summaryCategory').textContent = selectedText || '-';
                updateSummary();
            });

            // Character counter
            descriptionInput.addEventListener('input', function() {
                charCount.textContent = this.value.length;
                if (this.value.length > 500) {
                    this.value = this.value.substring(0, 500);
                }
            });

            // Drag & Drop upload
            uploadArea.addEventListener('click', () => attachmentInput.click());

            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('drag-over');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('drag-over');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('drag-over');
                if (e.dataTransfer.files.length) {
                    attachmentInput.files = e.dataTransfer.files;
                    handleFileSelect();
                }
            });

            attachmentInput.addEventListener('change', handleFileSelect);

            function handleFileSelect() {
                if (attachmentInput.files.length > 0) {
                    const validTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    
                    if (attachmentInput.files.length > 5) {
                        alert('Maksimal 5 file yang dapat diunggah sekaligus.');
                        attachmentInput.value = '';
                        filePreview.style.display = 'none';
                        uploadArea.style.display = 'block';
                        return;
                    }

                    const fileListContainer = document.getElementById('fileListContainer');
                    fileListContainer.innerHTML = '';
                    
                    let hasError = false;

                    Array.from(attachmentInput.files).forEach(file => {
                        if (!validTypes.includes(file.type)) {
                            alert('Format file tidak didukung: ' + file.name + '. Gunakan PDF, JPG, atau PNG.');
                            hasError = true;
                        }

                        if (file.size > maxSize) {
                            alert('Ukuran file terlalu besar: ' + file.name + '. Maksimal 5MB per file.');
                            hasError = true;
                        }
                        
                        if (!hasError) {
                            const fileSizeStr = (file.size / 1024).toFixed(2) + ' KB';
                            const fileHtml = `
                                <div class="alert alert-info d-flex justify-content-between align-items-center mb-0 py-2">
                                    <div>
                                        <i class="bi bi-file-earmark-check me-2"></i>
                                        <strong>${file.name}</strong>
                                        <small class="text-muted ms-2">${fileSizeStr}</small>
                                    </div>
                                </div>
                            `;
                            fileListContainer.insertAdjacentHTML('beforeend', fileHtml);
                        }
                    });

                    if (hasError) {
                        attachmentInput.value = '';
                        filePreview.style.display = 'none';
                        uploadArea.style.display = 'block';
                        return;
                    }

                    filePreview.style.display = 'block';
                    uploadArea.style.display = 'none';
                }
            }

            removeFileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                attachmentInput.value = '';
                filePreview.style.display = 'none';
                uploadArea.style.display = 'block';
                document.getElementById('fileListContainer').innerHTML = '';
            });

            function updateSummary() {
                const amount = parseInt(amountInput.value) || 0;
                document.getElementById('summaryAmount').textContent = 'Rp ' + amount.toLocaleString('id-ID');
            }

            function openSubmitModal() {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                modalEl.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-open');

                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'submitModalBackdrop';
                document.body.appendChild(backdrop);
            }

            function closeSubmitModal() {
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                modalEl.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');

                const backdrop = document.getElementById('submitModalBackdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            }

            submitButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }
                actionInput.value = 'submit';
                openSubmitModal();
            });

            confirmSubmitBtn.addEventListener('click', function() {
                closeSubmitModal();
                form.submit();
            });

            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(button) {
                button.addEventListener('click', closeSubmitModal);
            });

            modalEl.addEventListener('click', function(e) {
                if (e.target === modalEl) {
                    closeSubmitModal();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSubmitModal();
                }
            });

            // Form validation
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    </script>
</x-app-layout>

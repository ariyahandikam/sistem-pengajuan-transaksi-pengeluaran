<x-app-layout>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i> Input Anggaran Baru
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('budgets.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="year" class="form-label">Tahun <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('year') is-invalid @enderror" id="year" name="year" min="2020" max="2099" value="{{ old('year', $selectedYear) }}" required>
                            @error('year')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Budget Info Display -->
                        <div id="budgetInfoContainer" class="mb-3" style="display: none;">
                            <div class="card bg-light border">
                                <div class="card-body p-3">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <div>
                                                <small class="text-muted d-block">Total Anggaran</small>
                                                <h6 class="text-dark fw-bold" id="totalBudgetDisplay">Rp 0</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <small class="text-muted d-block">Anggaran Terpakai</small>
                                                <h6 class="text-danger fw-bold" id="usedBudgetDisplay">Rp 0</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <small class="text-muted d-block">Sisa Anggaran</small>
                                                <h6 class="text-success fw-bold" id="remainingBudgetDisplay">Rp 0</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="total_budget" class="form-label">Nominal Anggaran (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control @error('total_budget') is-invalid @enderror" id="total_budget" name="total_budget" placeholder="0" value="{{ old('total_budget') }}" required>
                            </div>
                            @error('total_budget')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="d-block text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Format: 50000000 (tanpa titik atau koma)<br>
                                <strong>Nominal ini akan DITAMBAHKAN ke anggaran kategori yang sudah ada</strong>
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Simpan Anggaran
                            </button>
                            <a href="{{ route('budgets.index', ['year' => $selectedYear]) }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i> Informasi
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Cara Menggunakan:</strong></p>
                    <ol>
                        <li>Pilih tahun untuk anggaran</li>
                        <li>Pilih kategori pengeluaran</li>
                        <li>Masukkan nominal anggaran yang ingin ditambahkan</li>
                        <li>Klik "Simpan Anggaran"</li>
                    </ol>
                    <div class="alert alert-info mt-4">
                        <small>
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Cara Kerja:</strong> Nominal yang Anda input akan <strong>DITAMBAHKAN</strong> ke anggaran kategori yang sudah ada di tahun tersebut.<br><br>
                            Contoh: Jika kategori "Operasional" tahun 2026 sudah memiliki anggaran Rp 5.000.000, dan Anda input Rp 2.000.000, maka total anggaran akan menjadi Rp 7.000.000
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_id');
            const yearInput = document.getElementById('year');
            const budgetInfoContainer = document.getElementById('budgetInfoContainer');
            const totalBudgetDisplay = document.getElementById('totalBudgetDisplay');
            const usedBudgetDisplay = document.getElementById('usedBudgetDisplay');
            const remainingBudgetDisplay = document.getElementById('remainingBudgetDisplay');

            function loadBudgetInfo() {
                if (!categorySelect.value) {
                    budgetInfoContainer.style.display = 'none';
                    return;
                }

                const year = yearInput.value || new Date().getFullYear();
                const categoryId = categorySelect.value;

                fetch(`{{ route('budgets.info') }}?category_id=${categoryId}&year=${year}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            totalBudgetDisplay.textContent = 'Rp ' + data.total_budget.toLocaleString('id-ID');
                            usedBudgetDisplay.textContent = 'Rp ' + data.used_budget.toLocaleString('id-ID');
                            remainingBudgetDisplay.textContent = 'Rp ' + data.remaining_budget.toLocaleString('id-ID');
                        } else {
                            totalBudgetDisplay.textContent = 'Rp 0';
                            usedBudgetDisplay.textContent = 'Rp 0';
                            remainingBudgetDisplay.textContent = 'Rp 0';
                        }
                        budgetInfoContainer.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        budgetInfoContainer.style.display = 'none';
                    });
            }

            // Load budget info when category changes
            if (categorySelect) {
                categorySelect.addEventListener('change', loadBudgetInfo);
                yearInput.addEventListener('change', loadBudgetInfo);
                
                // Load on page load if category is already selected
                if (categorySelect.value) {
                    loadBudgetInfo();
                }
            }
        });
    </script>
</x-app-layout>

<x-app-layout>
    
    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4 gap-2 fade-in-up" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="budgets-tab" data-bs-toggle="pill" data-bs-target="#budgets-content" type="button" role="tab">
                <i class="bi bi-wallet2 me-2"></i>Daftar Anggaran
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4" id="input-tab" data-bs-toggle="pill" data-bs-target="#input-content" type="button" role="tab">
                <i class="bi bi-plus-circle me-2"></i>Input Anggaran
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4" id="categories-tab" data-bs-toggle="pill" data-bs-target="#categories-content" type="button" role="tab">
                <i class="bi bi-bookmark-fill me-2"></i>Kategori
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content fade-in-up" id="pills-tabContent">
        
        <!-- Tab 1: Budget List -->
        <div class="tab-pane fade show active" id="budgets-content" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        Anggaran per Kategori ({{ $selectedYear }})
                    </h5>
                    <div>
                        <select id="yearFilter" class="form-select form-select-sm rounded-pill shadow-sm bg-light" style="width: 120px;" onchange="location.href='?year=' + this.value">
                            @php
                                $currentYear = date('Y');
                                for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                                    echo "<option value=\"$i\" " . ($selectedYear == $i ? 'selected' : '') . ">$i</option>";
                                }
                            @endphp
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($budgetsByCategory->isEmpty())
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-wallet2 fs-1 d-block mb-3"></i>
                            <p class="fs-5 mb-0">Belum ada data anggaran tahun {{ $selectedYear }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Kategori</th>
                                        <th class="text-end">Total Anggaran</th>
                                        <th class="text-end">Terpakai</th>
                                        <th class="text-end">Sisa</th>
                                        <th style="width: 25%;">Penggunaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $allCategories = array_keys($budgetsByCategory->toArray());
                                        $categoryColors = \App\Helpers\CategoryColorHelper::getAllCategoryColors($allCategories);
                                        $grandTotal = 0;
                                        $grandUsed = 0;
                                        $grandRemaining = 0;
                                    @endphp
                                    @foreach($budgetsByCategory as $categoryName => $budgets)
                                        @php
                                            $totalBudget = $budgets->sum('total_budget');
                                            $usedBudget = $budgets->sum('used_budget');
                                            $remainingBudget = $totalBudget - $usedBudget;
                                            $percentage = $totalBudget > 0 ? round(($usedBudget / $totalBudget) * 100, 2) : 0;
                                            
                                            $grandTotal += $totalBudget;
                                            $grandUsed += $usedBudget;
                                            $grandRemaining += $remainingBudget;
                                            $colors = $categoryColors[$categoryName] ?? ['badge' => '#4F46E5', 'progress' => '#818CF8'];
                                            
                                            $progressColor = $percentage <= 50 ? 'bg-success' : ($percentage <= 80 ? 'bg-warning' : 'bg-danger');
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge" style="background-color: {{ $colors['badge'] }};">
                                                    {{ $categoryName }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-medium text-dark">Rp {{ number_format($totalBudget, 0, ',', '.') }}</td>
                                            <td class="text-end text-secondary">Rp {{ number_format($usedBudget, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                <span class="fw-bold {{ $remainingBudget < 0 ? 'text-danger' : 'text-success' }}">
                                                    Rp {{ number_format($remainingBudget, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="pe-4 align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                        <div class="progress-bar {{ $progressColor }}" style="width: {{ min($percentage, 100) }}%"></div>
                                                    </div>
                                                    <span class="small fw-bold text-muted">{{ $percentage }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-light">
                                        <td class="ps-4 fw-bold text-dark">TOTAL KESELURUHAN</td>
                                        <td class="text-end fw-bold text-primary">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-secondary">Rp {{ number_format($grandUsed, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold {{ $grandRemaining < 0 ? 'text-danger' : 'text-success' }}">
                                            Rp {{ number_format($grandRemaining, 0, ',', '.') }}
                                        </td>
                                        <td class="pe-4 align-middle">
                                            @php $grandPercentage = $grandTotal > 0 ? round(($grandUsed / $grandTotal) * 100, 2) : 0; @endphp
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ min($grandPercentage, 100) }}%"></div>
                                                </div>
                                                <span class="small fw-bold text-dark">{{ $grandPercentage }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab 2: Input Budget -->
        <div class="tab-pane fade" id="input-content" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 fw-bold">Tambah / Update Anggaran</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('budgets.store') }}" method="POST">
                                @csrf
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Tahun</label>
                                        <select class="form-select" id="year" name="year" required>
                                            @php
                                                $y = date('Y');
                                                for($i=$y-1; $i<=$y+2; $i++) {
                                                    echo "<option value=\"$i\" " . ($selectedYear == $i ? 'selected' : '') . ">$i</option>";
                                                }
                                            @endphp
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Kategori</label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach(\App\Models\Category::orderBy('name')->get() as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Current Budget Preview -->
                                <div id="budgetInfoContainer" class="bg-light p-3 rounded-3 mb-4" style="display: none;">
                                    <h6 class="text-muted small fw-bold mb-3">INFO ANGGARAN SAAT INI</h6>
                                    <div class="row text-center g-2">
                                        <div class="col-4 border-end">
                                            <div class="small text-muted">Total</div>
                                            <div class="fw-bold text-dark" id="totalBudgetDisplay">Rp 0</div>
                                        </div>
                                        <div class="col-4 border-end">
                                            <div class="small text-muted">Terpakai</div>
                                            <div class="fw-bold text-danger" id="usedBudgetDisplay">Rp 0</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="small text-muted">Sisa</div>
                                            <div class="fw-bold text-success" id="remainingBudgetDisplay">Rp 0</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-medium">Nominal Penambahan Anggaran (Rp)</label>
                                    <div class="input-group input-group-lg shadow-sm">
                                        <span class="input-group-text bg-white border-end-0">Rp</span>
                                        <input type="number" class="form-control border-start-0 ps-0" id="total_budget" name="total_budget" required placeholder="0" min="1">
                                    </div>
                                    <div class="form-text mt-2 text-info">
                                        <i class="bi bi-info-circle me-1"></i> Nominal ini akan ditambahkan ke saldo anggaran.
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm rounded-pill">
                                    <i class="bi bi-check-lg me-2"></i>Simpan Anggaran
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="card bg-primary text-white shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4 d-flex flex-column justify-content-center">
                            <i class="bi bi-lightbulb text-white-50 fs-1 mb-3"></i>
                            <h5 class="fw-bold mb-3">Cara Mengisi Anggaran</h5>
                            <p class="text-white-75 mb-4">Sistem menggunakan metode top-up/akumulasi. Nominal yang Anda masukkan akan ditambahkan ke total anggaran kategori pada tahun yang dipilih.</p>
                            
                            <div class="bg-white bg-opacity-10 rounded-3 p-3 mt-auto">
                                <h6 class="fw-bold">Contoh:</h6>
                                <p class="small mb-0 opacity-75">Kategori "Marketing" 2026 memiliki Rp 10.000.000. Jika Anda input Rp 5.000.000, maka totalnya menjadi Rp 15.000.000.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Manage Categories -->
        <div class="tab-pane fade" id="categories-content" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 fw-bold">Tambah Kategori Baru</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('categories.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Nama Kategori</label>
                                    <input type="text" class="form-control" name="name" required placeholder="Cth: ATK, Operasional">
                                </div>
                                <div class="mb-4">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_po_produk" id="isPoProduk" value="1">
                                        <label class="form-check-label fw-medium" for="isPoProduk">Kategori PO Produk</label>
                                    </div>
                                    <div class="form-text small mt-1">Aktifkan jika kategori digunakan untuk Purchase Order barang produksi.</div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                    Simpan Kategori
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-7">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 fw-bold">Daftar Kategori</h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach(\App\Models\Category::orderBy('name')->get() as $category)
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                        <div class="fw-medium text-dark">{{ $category->name }}</div>
                                        @if($category->is_po_produk)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">PO Produk</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3">Regular</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
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
                            budgetInfoContainer.style.display = 'block';
                        } else {
                            budgetInfoContainer.style.display = 'none';
                        }
                    })
                    .catch(() => {
                        budgetInfoContainer.style.display = 'none';
                    });
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', loadBudgetInfo);
                yearInput.addEventListener('change', loadBudgetInfo);
                if (categorySelect.value) {
                    loadBudgetInfo();
                }
            }
        });
    </script>
    @endpush
</x-app-layout>

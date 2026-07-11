<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Pengajuan Transaksi Pengeluaran') }}</title>
    
    <link rel="icon" href="{{ asset('images/logo2.jpg') }}">

    <!-- Google Fonts included via CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<!-- Navbar (Glassmorphism) -->
<nav class="navbar navbar-expand-md navbar-light navbar-custom fixed-top">
    <div class="container-fluid px-4">
        <!-- Sidebar Toggle (Mobile) -->
        <button class="btn btn-link d-md-none text-dark me-2" id="sidebarToggle" type="button">
            <i class="bi bi-list fs-3"></i>
        </button>

        <a class="navbar-brand d-none d-md-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Lavanaya Madinah Travel" height="35" class="me-2">
        </a>

        <!-- Mobile Brand -->
        <a class="navbar-brand d-md-none fw-bold text-primary mx-auto" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Lavanaya Madinah Travel" height="30">
        </a>

        <div class="d-flex align-items-center ms-auto">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center text-dark" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar me-2 shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="d-none d-md-inline fw-medium">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" aria-labelledby="userDropdown">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <div class="text-muted small">{{ Auth::user()->email }}</div>
                    </li>
                    <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2 text-muted"></i>Profile Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="dropdown-item p-0">
                            @csrf
                            <button type="submit" class="btn btn-link text-decoration-none text-danger w-100 text-start py-2">
                                <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<nav id="sidebarMenu" class="sidebar">
    <div class="p-4 d-md-none">
        <!-- Close button for mobile -->
        <button class="btn btn-link text-white-50 float-end p-0" id="sidebarClose" type="button" onclick="document.getElementById('sidebarMenu').classList.remove('show')">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="clearfix"></div>
    </div>
    
    <div class="sidebar-label mt-3 mt-md-4">Menu Utama</div>
    <ul class="nav flex-column mb-4">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        </li>

        @if(Auth::user()->hasRole('staff'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('submissions.*') ? 'active' : '' }}" href="{{ route('submissions.index') }}">
                    <i class="bi bi-file-earmark-text"></i> Pengajuan Saya
                </a>
            </li>
        @endif

        @if(in_array(Auth::user()->roleSlug, ['spv', 'manager', 'direktur', 'finance']))
            @php
                $pendingApprovalsCount = 0;
                $targetStatus = match (Auth::user()->roleSlug) {
                    'spv'      => \App\Models\Submission::STATUS_WAITING_SPV,
                    'manager'  => \App\Models\Submission::STATUS_WAITING_MANAGER,
                    'direktur' => \App\Models\Submission::STATUS_WAITING_DIREKTUR,
                    'finance'  => \App\Models\Submission::STATUS_WAITING_FINANCE,
                    default    => null,
                };
                if ($targetStatus) {
                    $pendingApprovalsCount = \App\Models\Submission::where('status', $targetStatus)->count();
                }
            @endphp
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-between {{ (request()->routeIs('approvals.*') && ! request()->routeIs('approvals.history.*')) ? 'active' : '' }}" href="{{ route('approvals.index') }}">
                    <div><i class="bi bi-check2-square me-1"></i> Persetujuan</div>
                    @if($pendingApprovalsCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $pendingApprovalsCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('approvals.history.*') ? 'active' : '' }}" href="{{ route('approvals.history.index') }}">
                    <i class="bi bi-clock-history"></i> Riwayat Persetujuan
                </a>
            </li>
        @endif
    </ul>

    @if(Auth::user()->hasRole('finance'))
        <div class="sidebar-label">Keuangan</div>
        <ul class="nav flex-column mb-4">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}" href="{{ route('budgets.index') }}">
                    <i class="bi bi-wallet2"></i> Kelola Anggaran
                </a>
            </li>
        </ul>
    @endif

    @if(Auth::user()->hasRole('admin'))
        <div class="sidebar-label">Administrator</div>
        <ul class="nav flex-column mb-4">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i> User Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.activitylogs.*') ? 'active' : '' }}" href="{{ route('admin.activitylogs.index') }}">
                    <i class="bi bi-journal-text"></i> Activity Log
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.audit-trail.*') ? 'active' : '' }}" href="{{ route('admin.audit-trail.index') }}">
                    <i class="bi bi-shield-check"></i> Audit Trail
                </a>
            </li>
        </ul>
    @endif
</nav>

<!-- Main Content -->
<main class="main-content" id="mainContent">
    <!-- Header/Breadcrumb -->
    @if (isset($header))
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold text-dark mb-0">
                {{ $header }}
            </h4>
            <div class="d-none d-md-block text-muted small">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
    @endif

    <!-- Alert Messages (Toast style) -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center fade-in-up" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-3 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center fade-in-up" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-3 text-danger"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Content Slot -->
    @isset($slot)
        {{ $slot }}
    @else
        @yield('content')
    @endisset
</main>

@stack('scripts')
<script>
    // Auto dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
</body>
</html>

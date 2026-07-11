<x-guest-layout>
    <div class="text-center mb-4">
        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
            <i class="bi bi-key-fill text-primary fs-4"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Lupa Password?</h3>
        <p class="text-muted small px-2">
            Tidak masalah. Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 alert alert-success border-0 rounded-3 small" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label fw-semibold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-envelope-fill me-1 text-primary"></i> Alamat Email
            </label>
            <div class="auth-input-wrapper">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input id="email" type="email" class="form-control auth-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@perusahaan.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary-custom btn-lg">
                <i class="bi bi-send-fill me-2"></i> Kirim Link Reset Password
            </button>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-muted small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
            </a>
        </div>
    </form>

    <style>
        .auth-input-wrapper {
            position: relative;
        }

        .auth-input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            z-index: 2;
            transition: color 0.3s ease;
            pointer-events: none;
        }

        .auth-input {
            padding: 14px 16px 14px 46px;
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            font-size: 0.95rem;
            background: #f8fafc;
            transition: all 0.3s ease;
            color: #1e293b;
        }

        .auth-input:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), 0 4px 12px rgba(59, 130, 246, 0.08);
        }

        .auth-input-wrapper:focus-within .auth-input-icon {
            color: #3b82f6;
        }

        .auth-input::placeholder {
            color: #cbd5e1;
            font-weight: 400;
        }
    </style>
</x-guest-layout>
